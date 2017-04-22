<?php

/**
 * This file is part of the Nextras community extensions of Nette Framework
 *
 * @license    New BSD License
 * @link       https://github.com/nextras/migrations
 */

namespace Nextras\Migrations;

use Nextras\Migrations\Engine\ExecutionPlanEntry;


/**
 * Marker interface.
 */
interface Exception
{
}


/**
 * Error in usage or implementation.
 */
class LogicException extends \LogicException implements Exception
{
}


/**
 * Error in Execution plan
 */
class ExecutionPlanException extends LogicException
{

	public static function orderNotSequential(ExecutionPlanEntry $entry, int $expectedOrd): self
	{
		return new self("Execution plan is invalid: entry {$entry} "
			. "has order {$entry->getOrd()}, expected $expectedOrd.");
	}


	public static function orderNotUnique(ExecutionPlanEntry $entryA, ExecutionPlanEntry $entryB): self
	{
		return new self("Execution plan is invalid: duplicate order {$entryA->getOrd()} in '{$entryA}', '{$entryB}'.");
	}


	public static function resolvedOrderMismatch(string $reason)
	{
		return new self("Execution plan does not matched order resolved from migration files: $reason.");
	}

}


/**
 * Error during runtime.
 */
abstract class RuntimeException extends \RuntimeException implements Exception
{
}


/**
 * Executing migration has failed.
 */
class ExecutionException extends RuntimeException
{
}


/**
 * Permission denied, file not found...
 */
class IOException extends RuntimeException
{
}


/**
 * Lock cannot be released or acquired.
 */
class LockException extends RuntimeException
{
}
