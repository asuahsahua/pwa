<?php

namespace Bot;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Psr\Log\LoggerTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Bot implements ContainerAwareInterface
{
	use ContainerAwareTrait;
	use LoggerTrait;

	/** @var Client */
	protected $discord;
	/** @var Command[] */
	protected $commands = [];

	public function __construct($options = [], Container $container = null)
	{
		if (!isset($options['prefix'])) {
			$options['prefix'] = '!';
		}

		$this->setContainer($container);

		$options['defaultHelpCommand'] = false;

		$this->discord = new Client($options);

		$this->registerHandlers();
		$this->registerCommands();
	}

	public function run()
	{
		$this->info("Running...");
		$this->discord->run();
	}

	protected function registerHandlers()
	{
		$this->discord->on('ready', function (Discord $discord) {
			$this->info("Bot ready!");

			$discord->on('message', function (Message $message, Discord $discord) {
				$this->info("{$message->author->username}: {$message->content}");
			});
		});
	}

	protected function registerCommands()
	{
		$commands = glob(__DIR__ . "/Commands/*.php");
		$commands = array_map(function($file) {
			return '\\Bot\\Commands\\' . basename($file, '.php');
		}, $commands);
		$commands = array_filter($commands, function($class) {
			return class_exists($class) && is_subclass_of($class, Command::class);
		});

		foreach ($commands as $commandClass) {
			/** @var Command $command */
			$command = new $commandClass($this->discord, $this->container);
			$this->commands[$commandClass] = $command;

			$this->discord->registerCommand(
				$command->getCommand(),
				function(Message $message, $args) use ($command) {
					$this->dispatch($command, $message, $args);
				},
				$command->getOptions()
			);

			$this->debug("Registered {$command->getCommand()}");
		}
	}

	public function log($level, $message, array $context = array())
	{
		$this->container->get('logger')->log($level, $message, $context);
	}

	/**
	 * Dispatch a message to the command
	 *
	 * @param Command $command
	 * @param Message $message
	 * @param         $args
	 */
	public function dispatch(Command $command, Message $message, $args)
	{
		// check if database is connected
		$conn = $this->container->get('doctrine.dbal.connection');
		if ($conn->ping() === false) {
			$conn->close();
			$conn->connect();
		}

		try {
			$command->reply($message, $args);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
	}
}