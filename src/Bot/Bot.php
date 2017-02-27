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

		$this->discord = new Client($options, $container);

		$this->registerCommands();
	}

	public function run()
	{
		$this->info("Running...");
		$this->discord->run();
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

			$this->discord->registerCommand($command);

			$this->debug("Registered {$commandClass} ({$this->discord->getPrefix()}{$command->getCommand()})");
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
		$conn = $this->container->get('doctrine.orm.default_entity_manager')->getConnection();
		if (!$conn->ping()) {
			$this->info("Connection seems down, attempting to reconnect...");
			$conn->close();
			$conn->connect();
		} else {
			$this->info("Connection seems to be up?");
		}

		try {
			$command->handle($message, $args);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
			$message->channel->sendMessage("Something went wrong - check logs :crying_cat_face:");
		}
	}
}