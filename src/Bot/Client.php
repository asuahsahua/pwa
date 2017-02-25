<?php

namespace Bot;

use Discord\DiscordCommandClient;

class Client extends DiscordCommandClient
{
	public function getPrefix()
	{
		return str_replace((string) $this->user, '@'.$this->username, $this->commandClientOptions['prefix']);
	}

	public function getAliases()
	{
		return $this->aliases;
	}

	/**
	 * @return \Discord\CommandClient\Command[]
	 */
	public function getCommands()
	{
		return $this->commands;
	}
}