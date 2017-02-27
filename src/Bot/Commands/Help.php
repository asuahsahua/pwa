<?php

namespace Bot\Commands;

use Discord\Parts\Channel\Message;
use Bot\Command;

class Help extends Command
{
	public function handle(Message $message, string $content)
	{
		if (!empty($content)) {
			$this->sendCommandHelp($message, $content);
		} else {
			$this->sendAllCommandsHelp($message);
		}
	}

	protected function sendCommandHelp(Message $message, $commandString)
	{
		$prefix = $this->client->getPrefix();

		$command = $this->client->getCommand($commandString);

		if (is_null($command)) {
			$message->channel->sendMessage("The command {$commandString} does not exist.");
			return;
		}

		$help = $command->getHelp($prefix);

		$response = "```\r\n";
		$response .= $help['text'];

		$commandAliases = [];
		foreach ($this->client->getAliases() as $alias => $command) {
			if ($command == $commandString) {
				$commandAliases []= $alias;
			}
		}

		if (count($commandAliases)) {
			$response .= "\r\nAliases: " . implode(', ', $commandAliases);
		}

		$response .= '```';

		$message->channel->sendMessage($response);
	}

	private function sendAllCommandsHelp($message)
	{
		$prefix = $this->client->getPrefix();

		$response = "```\r\n";
		$response .= "Domia Bot - Maybe useful stuff bot thing\r\n\r\n";

		foreach ($this->client->getCommands() as $command) {
			$help = $command->getHelp($prefix);
			$response .= $help['text'];
		}

		$response .= "\r\nRun '{$prefix}help command' to get more information about a specific function.\r\n";
		$response .= '```';

		$message->channel->sendMessage($response);
	}

	public function getCommand() : string
	{
		return 'help';
	}

	public function getDescription()
	{
		return "Provides a list of commands available.";
	}

	public function getUsage()
	{
		return "[command]";
	}
}