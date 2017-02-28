<?php

namespace AppBundle\Container;

use Psr\Log\LoggerTrait;

trait ContainerAwareTrait
{
	use LoggerTrait;
	use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

	public function log($level, $message, array $context = array())
	{
		$this->container->get('logger')->log($level, $message, $context);
	}
}