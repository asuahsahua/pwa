<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiscordBotCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('discord:bot')
            ->setDescription('Start the Discord bot')
            ->setHelp('Starts the Discord bot');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bot = $this->getContainer()->get('discord.bot');
        $bot->setContainer($this->getContainer());
        $bot->run();
    }
}