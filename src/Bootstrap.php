<?php

declare(strict_types=1);

/**
 * @author Pavel Janda <me@paveljanda.com>
 * @copyright Copyright (c) 2020, Pavel Janda
 */

namespace Doctor\DI\Nette;

use Doctor\DI\Nette\Controller\ControllerProvider;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

final class Bootstrap
{

	private ContainerLoader $containerLoader;
	private bool $debugMode;
	private string $cacheDir;
	/** @var array<string> */
	private array $configFiles = [];


	public function __construct(string $cacheDir, bool $debugMode = true)
	{
		$this->containerLoader = new ContainerLoader($cacheDir, $debugMode);

		$this->setCacheDir($cacheDir);
		$this->setDebugMode($debugMode);
	}


	public function addConfigFile(string $configFile): void
	{
		$this->configFiles[] = $configFile;
	}


	public function createContainer(): Container
	{
		$class = $this->containerLoader->load(function(Compiler $compiler): void {
			$compiler->addConfig(['parameters' => [
				'cacheDir' => $this->cacheDir,
				'debugMode' => $this->debugMode,
			]]);

			foreach ($this->configFiles as $configFile) {
				$compiler->loadConfig($configFile);
			}

			$compiler->loadConfig(__DIR__ . '/nette-config.neon');
		}, $this->debugMode ? 'debug' : null);

		/** @var Container $container */
		$container = new $class;

		$container->getByType(ControllerProvider::class)->setContainer($container);

		return $container;
	}


	private function setCacheDir(string $cacheDir): void
	{
		$this->cacheDir = rtrim($cacheDir, '/');
		$routerCacheDir = $this->cacheDir . '/router';

		if (!is_dir($routerCacheDir)) {
			mkdir($routerCacheDir, 0777, true);

			if (!is_dir($routerCacheDir)) {
				throw new \RuntimeException(
					sprintf('Could not created cache directory %s', $routerCacheDir)
				);
			}
		}
	}


	private function setDebugMode(bool $debugMode): void
	{
		if ($debugMode) {
			$whoops = new Run;
			$whoops->pushHandler(new PrettyPageHandler);
			$whoops->register();
		}

		$this->debugMode = $debugMode;
	}
}
