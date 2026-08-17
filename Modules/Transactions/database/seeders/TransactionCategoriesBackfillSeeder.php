<?php

namespace Modules\Transactions\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Clinic\Models\Doctor;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Salary;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupAnalysis;
use Modules\Patients\Models\Hospitalization;
use Modules\Patients\Models\Operation;
use Modules\Patients\Models\Patient;
use Modules\Transactions\Constants\TransactionCategory;
use Modules\Transactions\Models\Transaction;

class TransactionCategoriesBackfillSeeder extends Seeder
{
  /**
   * Backfill transaction categories based on their transactionable/accountable relations.
   */
  public function run(): void
  {
    $updated = 0;

    DB::transaction(function () use (&$updated) {
      Transaction::query()->chunkById(500, function ($transactions) use (&$updated) {
        foreach ($transactions as $transaction) {
          $category = $this->resolveCategory($transaction);

          if ($category !== null && $transaction->category !== $category) {
            $transaction->update(['category' => $category]);
            $updated++;
          }
        }
      });
    });

    $this->command?->info("Backfilled category for {$updated} transactions.");
  }

  private function resolveCategory(Transaction $transaction): ?string
  {
    // 1- Resolve by accountable side first.
    $accountableCategory = match ($transaction->accountable_type) {
      Doctor::class => TransactionCategory::COMMISSION,
      InsuranceSociety::class => TransactionCategory::INSURANCE_COVERAGE,
      Employee::class => TransactionCategory::SALARY,
      default => null,
    };

    if ($accountableCategory !== null) {
      return $accountableCategory;
    }

    // 2- If accountable is a patient, resolve by the transactionable side.
    if ($transaction->accountable_type === Patient::class) {
      // 3- Checkup analyses can be either a regular analysis or radiology.
      if ($transaction->transactionable_type === CheckupAnalysis::class) {
        $analysisType = $transaction->transactionable?->type;

        return $analysisType === MedicalServiceTypes::RADIOLOGY
          ? TransactionCategory::RADIOLOGY
          : TransactionCategory::ANALYSIS;
      }

      // 4- Hospitalization can be a base stay or an extension, check the details.
      if ($transaction->transactionable_type === Hospitalization::class) {
        $details = strtolower((string) $transaction->details);

        if (str_contains($details, 'extension') || str_contains($details, 'final')) {
          return TransactionCategory::HOSPITALIZATION_EXTENSION;
        }

        return TransactionCategory::HOSPITALIZATION;
      }

      return match ($transaction->transactionable_type) {
        Checkup::class => TransactionCategory::CHECKUP,
        Operation::class => TransactionCategory::OPERATION,
        default => TransactionCategory::MISC,
      };
    }

    // No accountable relation: resolve by the transactionable side.
    $transactionableCategory = match ($transaction->transactionable_type) {
      Checkup::class => TransactionCategory::CHECKUP,
      Operation::class => TransactionCategory::OPERATION,
      Hospitalization::class => TransactionCategory::HOSPITALIZATION,
      Salary::class => TransactionCategory::SALARY,
      default => null,
    };

    if ($transactionableCategory !== null) {
      return $transactionableCategory;
    }

    if ($transaction->transactionable_type === CheckupAnalysis::class) {
      $analysisType = $transaction->transactionable?->type;

      return $analysisType === MedicalServiceTypes::RADIOLOGY
        ? TransactionCategory::RADIOLOGY
        : TransactionCategory::ANALYSIS;
    }

    return TransactionCategory::MISC;
  }
}
