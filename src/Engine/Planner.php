<?php

/**
 * This file is part of the Nextras community extensions of Nette Framework
 *
 * @license    New BSD License
 * @link       https://github.com/nextras/migrations
 */

namespace Nextras\Migrations\Engine;


class Planner
{

	/** @var string */
	private $planFile;

	/** @var OrderResolver */
	private $orderResolver;


	public function __construct(string $planFile, OrderResolver $orderResolver)
	{
		$this->planFile = $planFile;
		$this->orderResolver = $orderResolver;
	}


	public function ()
	{

	}

}
