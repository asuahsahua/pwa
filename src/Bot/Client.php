<?php

namespace Bot;

use AppBundle\Container\ContainerAwareTrait;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @property string id
 */
class Client extends Discord implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * An array of options passed to the client.
     *
     * @var array Options.
     */
    protected $commandClientOptions;

    /**
     * A map of the commands.
     *
     * @var Command[] Commands.
     */
    protected $commands = [];

    /**
     * A map of aliases for commands.
     *
     * @var array Aliases.
     */
    protected $aliases = [];

    /**
     * Constructs a new command client.
     *
     * @param array     $options An array of options.
     * @param Container $container
     */
    public function __construct(array $options = [], Container $container)
    {
        $this->commandClientOptions = $this->resolveCommandClientOptions($options);

        $discordOptions = array_merge($this->commandClientOptions['discordOptions'], [
            'token' => $this->commandClientOptions['token'],
        ]);

        $this->setContainer($container);

        parent::__construct($discordOptions);

        $this->on('ready', function () {
            $this->commandClientOptions['prefix'] = str_replace('@mention', (string)$this->user, $this->commandClientOptions['prefix']);
            $this->commandClientOptions['name'] = str_replace('<UsernamePlaceholder>', $this->username, $this->commandClientOptions['name']);

            $this->on('message', function (Message $message) {
                try {
                    $this->onMessage($message);
                } catch (\Exception $e) {
                    $this->error("Exception processing message: '{$message->content}': {$e->getMessage()}");
                }
            });
        });
    }

    /**
     * Registers a new command.
     *
     * @param Command $command The command handling class name
     * @return Command The command instance.
     * @throws \Exception
     */
    public function registerCommand($command)
    {
        $commandName = $command->getCommand();
        if (array_key_exists($commandName, $this->commands)) {
            throw new \Exception("A command with the name {$commandName} already exists.");
        }

        $this->commands[$commandName] = $command;

        foreach ($command->getAliases() as $alias) {
            $this->registerAlias($alias, $command);
        }

        return $command;
    }

    /**
     * Registers a command alias.
     *
     * @param string $alias   The alias to add.
     * @param string $command The command.
     */
    public function registerAlias($alias, $command)
    {
        $this->aliases[$alias] = $command;
    }

    /**
     * Attempts to get a command.
     *
     * @param string $command The command to get.
     * @param bool   $aliases Whether to search aliases as well.
     *
     * @return Command|null The command.
     */
    public function getCommand($command, $aliases = true)
    {
        if (array_key_exists($command, $this->commands)) {
            return $this->commands[$command];
        }

        if (array_key_exists($command, $this->aliases) && $aliases) {
            return $this->commands[$this->aliases[$command]];
        }

        return null;
    }

    /**
     * Builds a command and returns it.
     *
     * @param string $commandClass The command's class
     * @return Command
     * @throws \Exception
     */
    public function buildCommand($commandClass)
    {
        /** @var Command $command */
        return new $commandClass($this, $this->container);
    }

    /**
     * Resolves command options.
     *
     * @param array $options Array of options.
     *
     * @return array Options.
     */
    protected function resolveCommandOptions(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefined([
                'description',
                'usage',
                'aliases',
            ])
            ->setDefaults([
                'description' => 'No description provided.',
                'usage'       => '',
                'aliases'     => [],
            ]);

        $options = $resolver->resolve($options);

        if (!empty($options['usage'])) {
            $options['usage'] .= ' ';
        }

        return $options;
    }

    /**
     * Resolves the options.
     *
     * @param array $options Array of options.
     *
     * @return array Options.
     */
    protected function resolveCommandClientOptions(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired('token')
            ->setAllowedTypes('token', 'string')
            ->setDefined([
                'token',
                'prefix',
                'name',
                'description',
                'defaultHelpCommand',
                'discordOptions',
            ])
            ->setDefaults([
                'prefix'             => '@mention ',
                'name'               => '<UsernamePlaceholder>',
                'description'        => 'A bot made with DiscordPHP.',
                'defaultHelpCommand' => true,
                'discordOptions'     => [],
            ]);

        return $resolver->resolve($options);
    }

    public function getPrefix()
    {
        return str_replace((string)$this->user, '@' . $this->username, $this->commandClientOptions['prefix']);
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $this->container->get('logger')->log($level, $message, $context);
    }

    /**
     * Message handler, to dispatch to commands
     *
     * @param Message $message
     */
    private function onMessage(Message $message)
    {
        if ($message->author->id == $this->id) {
            return;
        }

        $this->info("MSG {$message->author->username}: {$message->content}");

        if (substr($message->content, 0, strlen($this->commandClientOptions['prefix'])) == $this->commandClientOptions['prefix']) {
            $withoutPrefix = substr($message->content, strlen($this->commandClientOptions['prefix']));
            $parts = preg_split('/ /', $withoutPrefix, 2);
            $command = count($parts) ? $parts[0] : "";

            if (array_key_exists($command, $this->commands)) {
                $command = $this->commands[$command];
            } elseif (array_key_exists($command, $this->aliases)) {
                $command = $this->commands[$this->aliases[$command]];
            } else {
                // Command doesn't exist.
                $this->info("Received {$command} but is not registered. Ignoring.");
                return;
            }

            // command is registered and we're good to go. do a pre-command check and dispatch it!
            $this->preReceiveCheck();
            $result = $command->receive($message, count($parts) > 1 ? $parts[1] : "");

            if (is_string($result)) {
                $message->reply($result);
            }
        }
    }

    protected function preReceiveCheck()
    {
        // Check the connection is up
        $conn = $this->container->get('doctrine.orm.default_entity_manager')->getConnection();
        if (!$conn->ping()) {
            $this->info("Connection seems down, attempting to reconnect...");
            $conn->close();
            $conn->connect();
        }
    }
}