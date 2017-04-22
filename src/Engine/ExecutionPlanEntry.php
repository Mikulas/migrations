<?php

/**
 * This file is part of the Nextras community extensions of Nette Framework
 *
 * @license    New BSD License
 * @link       https://github.com/nextras/migrations
 */

namespace Nextras\Migrations\Engine;


class ExecutionPlanEntry
{

	/** @var int */
	private $ord;

	/** @var string */
	private $group;

	/** @var string */
	private $fileName;

	/** @var string */
	private $hash;


	public function __construct(int $ord, string $group, string $fileName, string $hash)
	{
		$this->ord = $ord;
		$this->group = $group;
		$this->fileName = $fileName;
		$this->hash = $hash;
	}


	public static function unserialize(string $line): ExecutionPlanEntry
	{
		return new self(...explode("\t", $line));
	}


	public function getDisplayName(): string
	{
		return $this->group . ': ' . $this->fileName;
	}


	public function __toString(): string
	{
		return $this->getDisplayName();
	}


	public function serialize(): string
	{
		return implode("\t", [$this->ord, $this->group, $this->fileName, $this->hash]);
	}


	public function getOrd(): int
	{
		return $this->ord;
	}


	public function getGroup(): string
	{
		return $this->group;
	}


	public function getFileName(): string
	{
		return $this->fileName;
	}


	public function getHash(): string
	{
		return $this->hash;
	}

}
