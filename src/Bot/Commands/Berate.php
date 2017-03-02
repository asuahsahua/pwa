<?php

namespace Bot\Commands;

use Discord\Parts\Channel\Message;
use Bot\Command;

class Berate extends Command
{
    static $berates = [
        "Fuck your mechanics",
        "DPSing adds is for suckers who don't know what time it is",
        "I'm not AFK, I'm just not doing anything",
        "Sorry, my limit is 3 repair bots per 5 minutes",
        "Toss your own damn Azshara Salad",
        "What?  Were the last 500 prolonged powers not enough?",
        "Yes, I'm fucking logging. I'm always fucking logging.",
        "Shut up, Kethion.",
        "Shut up, Panch.",
        "You sure do a lot of DPS as a corpse on the ground.",
        "You can't heal with feelycrafting, asshole.",
        "Why don't you roll off another warglaive while you're at it?",
        "The world-famous coffee that you adore tastes like piss water.",
        "I'm sorry, is that Brimfield kiting a strider?  The fuck?",
        "Ashlyn-Bot cannot berate you right now, as she can't be bothered to be in this channel thanks to you.",
    ];

    public function handle(Message $message, string $content)
    {
        $berates = self::$berates;

        $content = trim($content);
        if ($content) {
            $berates = array_filter($berates, function($berate) use ($content) {
                return stripos($berate, $content) !== false;
            });
        }

        if (empty($berates)) {
            $message->reply("none matching :poop:");
            return;
        }

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