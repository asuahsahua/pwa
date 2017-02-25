<?php

namespace Bot;

use Discord\Parts\Channel\Message;

abstract class Command
{
	protected $client;

	public function __construct(Client $discord)
	{
		$this->client = $discord;
	}

	abstract public function getCommand() : string;

	abstract public function reply(Message $message, $args);

	abstract public function getDescription();

	public function getUsage()
	{
		return null;
	}

	public function getAliases()
	{
		return null;
	}

	public function getOptions()
	{
		$options = [
			'description' => $this->getDescription(),
			'usage' => $this->getUsage(),
			'aliases' => $this->getAliases(),
		];

		return array_filter($options, function($option) {
			return !is_null($option);
		});
	}
}