<?php

namespace Bot;

use Discord\Parts\Channel\Message;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class Command implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	protected $client;

	public function __construct(Client $discord, Container $container)
	{
		$this->client = $discord;
		$this->setContainer($container);
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

	/**
	 * @return \Doctrine\ORM\EntityManager|object
	 */
	protected function getEntityManager()
	{
		return $this->container->get('doctrine.orm.entity_manager');
	}

	/**
	 * @param string $name
	 * @return EntityRepository
	 */
	protected function getRepository($name)
	{
		return $this->container->get('doctrine')->getRepository($name);
	}
}