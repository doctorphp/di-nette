<?php

declare(strict_types=1);

/**
 * @author Pavel Janda <me@paveljanda.com>
 * @copyright Copyright (c) 2020, Pavel Janda
 */

namespace Doctor\DI\Nette\Controller;

use Doctor\Rest\Controller\Controller;
use Doctor\Rest\Controller\ControllerProviderInterface;
use Nette\DI\Container;

final class ControllerProvider implements ControllerProviderInterface
{

	private ?Container $container;


	public function setContainer(Container $container): void
	{
		$this->container = $container;
	}


	public function getByClass(string $class): Controller
	{
		if ($this->container === null) {
			throw new \RuntimeException('Container not set');
		}

		$controller = $this->container->getByType($class);

		if (!$controller instanceof Controller) {
			throw new \UnexpectedValueException(
				sprintf('%s should extend %s', $class, Controller::class)
			);
		}

		return $controller;
	}
}
