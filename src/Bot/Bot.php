<?php

namespace Bot;

use AppBundle\Container\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class Bot implements ContainerAwareInterface
{
	use ContainerAwareTrait;

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
}