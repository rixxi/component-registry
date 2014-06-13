<?php

namespace Rixxi\Application\UI\Presenter;


/**
 * Implements IRenderMode and handles render<Mode>() calls ie. {control name:view ... } in template.
 * Useful for having multiple render modes of component with only one render method (default).
 *
 * It is intended only for Nette\Application\UI\Presenter descendants.
 */
trait ComponentRegistry /* extends \Nette\Application\UI\Presenter */
{

	/**
	 * @inject
	 * @var \Rixxi\ComponentRegistry\ComponentRegistry
	 */
	public $componentRegistry;


	protected function createComponent($name)
	{
		return parent::createComponent($name) ?: $this->componentRegistry->createComponent($name);
	}

}
