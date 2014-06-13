<?php

namespace Rixxi\ComponentRegistry;

use Nette;
use Nette\DI\Container;


class ComponentRegistry extends Nette\Object
{

	/** @vat callback[] */
	public $onCreateComponent = [];

	/** @var Container */
	private $container;

	/** @var array */
	private $registry;


	public function __construct($registry, Container $container)
	{
		$this->registry = $registry;
		$this->container = $container;
	}


	public function createComponent($name)
	{
		if (isset($this->registry[$name])) {
			$component = $this->container->createService($this->registry[$name]);
			$this->onCreateComponent($component);
			return $component;
		}
	}

}
