<?php

namespace Bot\Commands;

use Discord\Parts\Channel\Message;
use Bot\Command;

class DelQuote extends Command
{
	public function reply(Message $message, $args)
	{
		$repo = $this->getRepository('AppBundle:Quote');

		$content = implode(' ', $args);

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

