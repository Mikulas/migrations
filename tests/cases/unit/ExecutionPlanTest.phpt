<?php

/**
 * @testCase
 */

namespace NextrasTests\Migrations;

use Nextras\Migrations\Engine\ExecutionPlan;
use Nextras\Migrations\Engine\ExecutionPlanEntry;
use Nextras\Migrations\Engine\ExecutionPlanPersister;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


class ExecutionPlanTest extends Tester\TestCase
{

	public function testPlanSerialization()
	{
		/** @var ExecutionPlanEntry[] $entries */
		$entries = [
			new ExecutionPlanEntry(1, 'test', 'foo.sql', md5('a')),
			new ExecutionPlanEntry(2, 'test2', 'bar.sql', md5('b')),
		];
		$plan = new ExecutionPlan($entries);

		Assert::matchFile(__DIR__ . '/plan.tsv', $plan->serialize());
	}


	public function testPersist()
	{
		$planFile = Tester\FileMock::create('', 'tsv');
		$persister = new ExecutionPlanPersister($planFile);

		$plan = $persister->readPlan();
		Assert::same([], iterator_to_array($plan));

		$plan->appendEntry(new ExecutionPlanEntry(1, 'test', 'foo.sql', md5('a')));
		$plan->appendEntry(new ExecutionPlanEntry(2, 'test2', 'bar.sql', md5('b')));
		$persister->writePlan($plan);

		$serialized = file_get_contents($planFile);
		Assert::matchFile(__DIR__ . '/plan.tsv', $serialized);
	}

}

$test = new ExecutionPlanTest();
$test->run();
