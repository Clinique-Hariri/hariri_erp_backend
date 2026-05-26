<?php

use Modules\MedicalReferences\Models\InsuranceSocietyBranch;

if (!function_exists('insurance_coverage_amount')) {
  function insurance_coverage_amount(?InsuranceSocietyBranch $insuranceSociety, float $amount): float
  {
    $coverage_percentage = $insuranceSociety?->coverage_percentage ?? 0;
    $coverage_percentage = max(0, min(100, $coverage_percentage));
    $coverage_amount = ($coverage_percentage / 100) * $amount;

    return round($coverage_amount, 2);
  }
}
