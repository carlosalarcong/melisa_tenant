<?php

namespace Rebsol\RecaudacionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Finder\Finder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RecaudacionExtension extends Extension   implements CompilerPassInterface {

	/**
	 * {@inheritDoc}
	 */
	public function load(array $configs, ContainerBuilder $container) {
		$configuration = new Configuration();
		// $config = $this->processConfiguration($configuration, $configs);
		$this->processConfiguration($configuration, $configs);

		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

		// Loading the services
		//$loader->load('services/controllers.yml');
		//$loader->load('services/event_listeners.yml');
		$loader->load('services/repositories.yml');
		$loader->load('services.yml');

		// Loading the parameters
		$loader->load('parameters.yml');

	}

	/**
	 * @author sDelgado
	 * [process Carga automicatemente todo las validaciones .yml
	 * de la carpeta validation en CajaBundle]
	 * @param  ContainerBuilder $container [description]
	 * @return files|yml                   [description]
	 */
	public function process(ContainerBuilder $container) {

		if($container->hasDefinition('validator.builder')){
			$dir    = $container->getParameter('kernel.root_dir') . '/../src/Rebsol/RecaudacionBundle/Resources/config/validation';
			$finder = new Finder();
			$finder->files()->name('*.yml')->in($dir);
			$files   = [];
			foreach ($finder as  $file) {
				$files[] =  $file->getRealPath();
			}
			if(count($files)){
				$container->getDefinition('validator.builder')->addMethodCall('addYamlMappings', [$files]);
			}
		}
	}

}
