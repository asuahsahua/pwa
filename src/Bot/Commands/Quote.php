<?php

namespace Bot\Commands;

use Discord\Parts\Channel\Message;
use Bot\Command;

class Quote extends Command
{
	public function handle(Message $message, string $content)
	{
		$repo = $this->getRepository('AppBundle:Quote');

		$query = $repo->createQueryBuilder('q')
			->addSelect('RAND() as HIDDEN rand')
			->addOrderBy('rand')
			->setMaxResults(1);

		if (!empty($content)) {
			$query
				->where('q.content LIKE :search')
				->setParameter('search', "%$content%");
		}

		/** @var \AppBundle\Entity\Quote $result */
		$result = $query->getQuery()->getOneOrNullResult();

		if (!$result) {
			$message->reply("no quote found");
		} else {
			$message->channel->sendMessage($result->getContent());
		}
	}

	public function getCommand() : string
	{
		return 'quote';
	}

	public function getDescription()
	{
		return "Get a quote from the database";
	}

	public function getUsage()
	{
		return "[term]";
	}
}

