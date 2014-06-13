<?php

namespace Rixxi\ComponentRegistry\DI;


interface IComponentFactoriesProvider
{

	/**
	 * Component names with factory classes
	 *
	 * return array(
	 * 	'Basket\Application\UI\BasketComponentFactory' => 'basket',
	 * 	'Avatar\Application\UI\AvatarComponentFactory::createAvatarComponent' => 'avatar',
	 * );
	 *
	 * @return array
	 */
	function getComponentFactories();

}
