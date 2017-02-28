<?php

namespace Bot\Commands;

use Discord\Parts\Channel\Message;
use Bot\Command;

class DelQuote extends Command
{
	public function handle(Message $message, string $content)
	{
		$message->reply("nah, not deleting it!");
		return;

		$repo = $this->getRepository('AppBundle:Quote');

		/** @var \AppBundle\Entity\Quote $result */
		$result = $repo->createQueryBuilder('q')
			->where('q.content = :content')
			->setParameter('content', $content)
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();

		if (!$result) {
			$message->reply("Could not find that quote");
			return;
		}

		$em = $this->getEntityManager();
		$em->remove($result);
		$em->flush();

		$message->reply("deleted!");
	}

	public function getCommand() : string
	{
		return 'delquote';
	}

	public function getDescription()
	{
		return "Delete a quote from the database";
	}

	public function getUsage()
	{
		return "[quote]";
	}
}

