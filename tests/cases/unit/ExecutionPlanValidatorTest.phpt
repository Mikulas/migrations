<?php

/**
 * @testCase
 */

namespace NextrasTests\Migrations;

use Nextras\Migrations\Engine\ExecutionPlan;
use Nextras\Migrations\Engine\ExecutionPlanEntry;
use Nextras\Migrations\Engine\ExecutionPlanValidator;
use Nextras\Migrations\Engine\OrderResolver;
use Nextras\Migrations\Engine\Runner;
use Nextras\Migrations\Entities\File;
use Nextras\Migrations\Entities\Group;
use Nextras\Migrations\ExecutionPlanException;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


class ExecutionPlanValidatorTest extends Tester\TestCase
{

	public function testValidation()
	{
		$resolvedOrder = $this->getResolvedOrder();
		$validator = new ExecutionPlanValidator();

		$okPlan = new ExecutionPlan([
			new ExecutionPlanEntry(0, 'test', 'a.sql', 'a.sql.md5'),
			new ExecutionPlanEntry(1, 'test', 'b.sql', 'b.sql.md5'),
		]);
		$validator->validate($okPlan, $resolvedOrder);
		Assert::true(TRUE, 'passed validation');


		$dupePlan = new ExecutionPlan([
			new ExecutionPlanEntry(0, 'test', 'a.sql', 'a.sql.md5'),
			new ExecutionPlanEntry(0, 'test', 'b.sql', 'b.sql.md5'),
		]);
		Assert::exception(function() use ($resolvedOrder, $dupePlan, $validator) {
			$validator->validate($dupePlan, $resolvedOrder);
		}, ExecutionPlanException::class);


		$nonSeqPlan = new ExecutionPlan([
			new ExecutionPlanEntry(0, 'test', 'a.sql', 'a.sql.md5'),
			new ExecutionPlanEntry(3, 'test', 'b.sql', 'b.sql.md5'),
		]);
		Assert::exception(function() use ($resolvedOrder, $nonSeqPlan, $validator) {
			$validator->validate($nonSeqPlan, $resolvedOrder);
		}, ExecutionPlanException::class);


		$hashMismatchPlan = new ExecutionPlan([
			new ExecutionPlanEntry(0, 'test', 'a.sql', 'a.sql.md5'),
			new ExecutionPlanEntry(1, 'test', 'b.sql', 'b.sql.md5XXX'),
		]);
		Assert::exception(function() use ($resolvedOrder, $hashMismatchPlan, $validator) {
			$validator->validate($hashMismatchPlan, $resolvedOrder);
		}, ExecutionPlanException::class);
	}


	private function getResolvedOrder(): array
	{
		$resolver = new OrderResolver;

		$groupA = $this->createGroup('test');
		$fileA = $this->createFile('a.sql', $groupA);
		$fileB = $this->createFile('b.sql', $groupA);

		return $resolver->resolve(
			[],
			[$groupA],
			[$fileA, $fileB],
			Runner::MODE_CONTINUE
		);
	}


	private function createFile($name, $group, $checksum = NULL)
	{
		$file = new File;
		$file->name = $name;
		$file->group = $group;
		$file->checksum = $checksum ?: "$name.md5";
		return $file;
	}


	private function createGroup($name, $enabled = TRUE, $deps = [])
	{
		$group = new Group;
		$group->name = $name;
		$group->enabled = $enabled;
		$group->dependencies = $deps;
		return $group;
	}

}

$test = new ExecutionPlanValidatorTest();
$test->run();
