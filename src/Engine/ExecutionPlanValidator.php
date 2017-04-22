<?php declare(strict_types=1);

namespace Nextras\Migrations\Engine;

use Nextras\Migrations\Entities\File;
use Nextras\Migrations\ExecutionPlanException;


class ExecutionPlanValidator
{

	public function validate(ExecutionPlan $plan, array $resolvedOrder)
	{
		$this->validateUniqueOrder($plan);
		$this->validateSequentialOrder($plan);
		$this->validateAgainstResolvedOrder($plan, $resolvedOrder);
	}


	/**
	 * @param ExecutionPlan $plan
	 * @return mixed|ExecutionPlanEntry
	 */
	private function validateUniqueOrder(ExecutionPlan $plan): void
	{
		$ord = [];
		foreach ($plan as $entry) {
			if (isset($ord[$entry->getOrd()])) {
				throw ExecutionPlanException::orderNotUnique($entry, $ord[$entry->getOrd()]);
			}

			$ord[$entry->getOrd()] = $entry;
		}
	}


	/**
	 * @throws ExecutionPlanException
	 */
	private function validateSequentialOrder(ExecutionPlan $plan): void
	{
		$expectedOrd = 0;
		foreach ($plan as $entry) {
			if ($entry->getOrd() !== $expectedOrd) {
				throw ExecutionPlanException::orderNotSequential($entry, $expectedOrd);
			}
			$expectedOrd++;
		}
	}


	/**
	 * @param ExecutionPlan $plan
	 * @param File[]        $resolvedOrder
	 * @throws  ExecutionPlanException
	 */
	private function validateAgainstResolvedOrder(ExecutionPlan $plan, array $resolvedOrder): void
	{
		foreach ($resolvedOrder as $ord => $file) {
			$plannedFile = $plan->getByOrd($ord);
			if ($plannedFile === NULL) {
				throw ExecutionPlanException::resolvedOrderMismatch("ord $ord not in plan");
			}

			// TODO
			if ($plannedFile->getHash() !== $file->checksum) {
				throw ExecutionPlanException::resolvedOrderMismatch("checksum mistmatch");
			}
			if ($plannedFile->getGroup() !== $file->group->name) {
				throw ExecutionPlanException::resolvedOrderMismatch("group mismatch");
			}
			if ($plannedFile->getFileName() !== $file->name) {
				throw ExecutionPlanException::resolvedOrderMismatch("filename mismatch");
			}
		}
	}

}
