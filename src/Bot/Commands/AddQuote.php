<?php

namespace Bot\Commands;

use AppBundle\Entity\Quote;
use Discord\Parts\Channel\Message;
use Bot\Command;

class AddQuote extends Command
{
	public function reply(Message $message, $args)
	{
		if (!count($args)) {
			$message->reply("you didn't give a quote.");
			return;
		}

		$content = implode(' ', $args);

		$quote = new Quote();
		$quote->setContent($content);
		$quote->setUser($message->author->username);
		$quote->setCreatedAt(new \DateTime());

		$em = $this->getEntityManager();
		$em->persist($quote);
		$em->flush();

		$message->reply("added!");
	}

	public function getCommand() : string
	{
		return 'addquote';
	}

	public function getDescription()
	{
		return "Adds a quote";
	}

	public function getUsage()
	{
		return "[quote]";
	}
}

