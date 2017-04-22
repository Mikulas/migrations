<?php

/**
 * This file is part of the Nextras community extensions of Nette Framework
 *
 * @license    New BSD License
 * @link       https://github.com/nextras/migrations
 */

namespace Nextras\Migrations\Engine;


use Nextras\Migrations\LogicException;

class ExecutionPlan implements \IteratorAggregate
{

	/** @var ExecutionPlanEntry[] */
	private $entries;


	public function __construct(array $entries)
	{
		$this->entries = $entries;
	}


	public static function unserialize(string $raw): ExecutionPlan
	{
		$lines = preg_split('~\r?\n\r?~', $raw, -1, PREG_SPLIT_NO_EMPTY);

		$entries = [];
		foreach ($lines as $line) {
			$entries[] = ExecutionPlanEntry::unserialize($line);
		}

		return new self($entries);
	}


	public function appendEntry(ExecutionPlanEntry $entry): void
	{
		$this->entries[] = $entry;
	}


	public function serialize(): string
	{
		$raw = '';
		foreach ($this->entries as $entry) {
			$raw .= $entry->serialize() . "\n";
		}
		return $raw;
	}


	/**
	 * @return \Iterator|ExecutionPlanEntry[]
	 */
	public function getIterator(): \Iterator
	{
		return new \ArrayIterator($this->entries);
	}


	public function getByOrd(int $ord): ?ExecutionPlanEntry
	{
		foreach ($this->entries as $entry) {
			if ($entry->getOrd() === $ord) {
				return $entry;
			}
		}
		return NULL;
	}

}
