<?php

/**
 * This file is part of the Nextras community extensions of Nette Framework
 *
 * @license    New BSD License
 * @link       https://github.com/nextras/migrations
 */

namespace Nextras\Migrations\Engine;

use Nette\Utils\FileSystem;


class ExecutionPlanPersister
{

	/** @var string */
	private $planFile;


	public function __construct(string $planFile)
	{
		$this->planFile = $planFile;
	}


	public function readPlan(): ExecutionPlan
	{
		// TODO add locking?
		$raw = file_get_contents($this->planFile);
		return ExecutionPlan::unserialize($raw);
	}


	public function writePlan(ExecutionPlan $plan): void
	{
		FileSystem::write($this->planFile, $plan->serialize(), NULL);
	}

}
