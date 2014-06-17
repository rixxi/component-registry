<?php

namespace Rixxi\ComponentRegistry\DI;

use Nette;
use Nette\DI\ServiceDefinition;
use Nette\Reflection\Method;
use Nette\Utils\Validators;


class ComponentRegistryExtension extends Nette\DI\CompilerExtension
{

	const DEFAULT_FACTORY_METHOD = 'create';

	const TAG_COMPONENT_REGISTRY = 'rixxi.componentRegistry';

	const FRAKKIN_KEY = 'fracktories';


	public $defaults = array(
		self::FRAKKIN_KEY => array(),
	);


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		Validators::assertField($config, self::FRAKKIN_KEY, 'array');

		foreach ($this->compiler->getExtensions('Rixxi\ComponentRegistry\DI\IComponentFactoriesProvider') as $provider) {
			/* @var IComponentFactoriesProvider $provider */
			$factories = $provider->getComponentFactories();
			Validators::assert($factories, 'array');
			$this->addComponentFactories($factories);
		}

		$this->addComponentFactories($config[self::FRAKKIN_KEY]);

		$this->getContainerBuilder()->addDefinition($this->prefix('service'))
			->setClass('Rixxi\ComponentRegistry\ComponentRegistry');
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		foreach ($registry = $builder->findByTag(self::TAG_COMPONENT_REGISTRY) as $name => $component) {
			Validators::assert($component, 'string', 'tag ' . self::TAG_COMPONENT_REGISTRY);
			/* @var ServiceDefinition $definition */
			$factory = $builder->getDefinition($name)->getFactory()->getEntity();
			if (strpos($factory[0], '@') === 0) {
				$class = substr($factory[0], 1);

				if (!$builder->getByType($class)) {
					$builder->addDefinition($this->prefix('registry.' . $component . '.factory')) // should be anonymous because nobody cares
						->setClass($class)
						->setAutowired(FALSE);
				}
			}
		}

		$builder->getDefinition($this->prefix('service'))
			->setArguments(array(array_flip($registry)));
	}


	private function addComponentFactories(array $factories)
	{
		$builder = $this->getContainerBuilder();

		foreach ($factories as $component => $factory) {
			if (strpos($factory, '::') === FALSE) {
				$factory .= '::' . self::DEFAULT_FACTORY_METHOD;
			}

			if ($builder->hasDefinition($name = $this->prefix('registry.' . $component))) {
				$definition = $builder->getDefinition($name);

			} else {
				$definition = $builder->addDefinition($name)
					->addTag(self::TAG_COMPONENT_REGISTRY, $component)
					->setAutowired(FALSE);
			}

			list($class, $method) = explode('::', $factory);
			if (!Method::from($class, $method)->isStatic()) {
				$factory = '@' . $factory;
			}
			$definition->setFactory($factory);
		}
	}

}
