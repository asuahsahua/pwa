<?php

namespace Bot;

use AppBundle\Container\ContainerAwareTrait;
use Discord\Parts\Channel\Message;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var Client */
    protected $client;

    /** @var Command[] Sub-commands */
    protected $subCommands = [];

    /** @var string[] Sub-command aliases, maps from alias to the sub-command trigger name */
    protected $subCommandAliases = [];


    /**
     * Creates a command instance.
     *
     * @param Client    $client The Discord Command Client.
     * @param Container $container
     */
    public function __construct(Client $client, Container $container)
    {
        $this->client = $client;
        $this->setContainer($container);
    }

    /**
     * Gets the trigger for this command
     *
     * @return string
     */
    abstract public function getCommand() : string;

    /**
     * Processes a command
     *
     * @param Message $message
     * @param string  $content
     * @return mixed
     */
    abstract public function handle(Message $message, string $content);

    /**
     * Returns the description of the command
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Returns the usage of the command
     *
     * @return string
     * @example "[arg1] [arg2]"
     */
    public function getUsage()
    {
        return null;
    }

    /**
     * Returns the aliases for this command
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return \Doctrine\ORM\EntityManager|object
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @param string $name
     * @return EntityRepository
     */
    protected function getRepository($name)
    {
        return $this->container->get('doctrine')->getRepository($name);
    }

    /**
     * Registers a new command.
     *
     * @param string $commandClass The command name.
     * @return Command The command instance.
     * @throws \Exception
     */
    public function registerSubCommand($commandClass)
    {
        $command = $this->client->buildCommand($commandClass);

        if (array_key_exists($command->getCommand(), $this->subCommands)) {
            throw new \Exception("A sub-command with the name {$command->getCommand()} already exists.");
        }

        $this->subCommands[$command->getCommand()] = $command;

        return $command;
    }

    /**
     * Registers a sub-command alias.
     *
     * @param string $alias   The alias to add.
     * @param string $command The command.
     */
    public function registerSubCommandAlias($alias, $command)
    {
        $this->subCommandAliases[$alias] = $command;
    }

    /**
     * Receives a command from the client.
     * Dispatches to handle() or to a sub-command's handle()
     *
     * @param Message $message The message.
     * @param string  $content The commands' body - everything after the command
     */
    public function receive(Message $message, string $content)
    {
        $parts = preg_split('/ /', $content, 2);

        $subCommand = count($parts) ? $parts[0] : null;

        // check sub-commands to see if the argument should be dispatched there
        if ($subCommand) {
            $command = null;
            if (array_key_exists($subCommand, $this->subCommands)) {
                $command = $this->subCommands[$subCommand];
            } elseif (array_key_exists($subCommand, $this->subCommandAliases)) {
                $command = $this->subCommands[$this->subCommandAliases[$subCommand]];
            }
            if ($command) {
                $subContent = count($parts) > 1 ? $parts[1] : "";
                $command->receive($message, $subContent);
                return;
            }
        }

        // if there was no valid sub-command, just process
        $this->handle($message, $content);
    }

    /**
     * Gets help for the command.
     *
     * @param string $prefix The prefix of the bot.
     *
     * @return string The help.
     */
    public function getHelp($prefix)
    {
        $helpString = "{$prefix}{$this->getCommand()} {$this->getUsage()} - {$this->getDescription()}\r\n";

        foreach ($this->subCommands as $command) {
            $help = $command->getHelp($prefix . $this->getCommand() . ' ');
            $helpString .= "    {$help['text']}\r\n";
        }

        return [
            'text'              => $helpString,
            'subCommandAliases' => $this->subCommandAliases,
        ];
    }

    /**
     * Handles dynamic get calls to the class.
     *
     * @param string $variable The variable to get.
     * @return mixed The value.
     * @throws \Exception
     */
    public function __get($variable)
    {
        $allowed = ['command', 'description', 'usage'];

        if (array_search($variable, $allowed) === false) {
            throw new \Exception("Invalid property: $variable");
        }

        return $this->{$variable};
    }
}