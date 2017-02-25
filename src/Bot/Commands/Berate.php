<?php

namespace Bot\Commands;

use Discord\Parts\Channel\Message;
use Bot\Command;

class Berate extends Command
{
	public function reply(Message $message, $args)
	{
		$berates = [
			"Fuck your mechanics",
			"DPSing adds is for suckers who don't know what time it is",
			"I'm not AFK, I'm just not doing anything",
			"Sorry, my limit is 3 repair bots per 5 minutes",
			"Toss your own damn Azshara Salad",
			"What?  Were the last 500 prolonged powers not enough?",
			"Yes, I'm fucking logging. I'm always fucking logging.",
			"Shut up, Kethion.",
			"Shut up, Panch.",
		];

		$key = array_rand($berates);

		$message->channel->sendMessage($berates[$key]);
	}

	public function getCommand() : string
	{
		return 'berate';
	}

	public function getDescription()
	{
		return "Berates you.";
	}
}