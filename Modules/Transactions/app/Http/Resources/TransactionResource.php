<?php

namespace Modules\Transactions\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\Clinic\Models\Doctor;
use Modules\HRM\Http\Resources\EmployeeResource;
use Modules\HRM\Http\Resources\SalaryResource;
use Modules\HRM\Models\Employee;
use Modules\HRM\Models\Salary;
use Modules\MedicalReferences\Http\Resources\InsuranceSocietyResource;
use Modules\MedicalReferences\Models\InsuranceSociety;
use Modules\Patients\Http\Resources\CheckupAnalysisResource;
use Modules\Patients\Http\Resources\CheckupResource;
use Modules\Patients\Http\Resources\HospitalizationResource;
use Modules\Patients\Http\Resources\OperationResource;
use Modules\Patients\Http\Resources\PatientResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupAnalysis;
use Modules\Patients\Models\Hospitalization;
use Modules\Patients\Models\Operation;
use Modules\Patients\Models\Patient;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Constants\Type;
use Modules\Transactions\Models\Transaction;

/** @mixin Transaction */
class TransactionResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'transaction_number' => $this->transaction_number,
      'amount' => $this->amount,
      'details' => $this->details,
      'type' => Type::get_resource($this->type),
      'status' => Status::get_resource($this->status),
      'next_statuses' => Status::get_next_statuses($this->status),
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'user_id' => $this->user_id,
      'user' => $this->whenLoaded('user', function () {
        return [
          'fullname' => $this->user->fullname,
        ];
      }),
      'transactionable_type' => match($this->transactionable_type) {
        Checkup::class => 'checkup',
        CheckupAnalysis::class => 'analysis',
        Operation::class => 'operation',
        Hospitalization::class => 'hospitalization',
        Salary::class => 'salary',
        default => null,
      },
      'accountable_type' => match($this->accountable_type) {
        Patient::class => 'patient',
        Doctor::class => 'doctor',
        InsuranceSociety::class => 'insurance',
        Employee::class => 'employee',
        default => null,
      },
      'transactionable' => match ($this->transactionable_type) {
        Checkup::class => new CheckupResource($this->whenLoaded('transactionable')),
        CheckupAnalysis::class => new CheckupAnalysisResource($this->whenLoaded('transactionable')),
        Operation::class => new OperationResource($this->whenLoaded('transactionable')),
        Hospitalization::class => new HospitalizationResource($this->whenLoaded('transactionable')),
        Salary::class => new SalaryResource($this->whenLoaded('transactionable')),
        default => null,
      },
      'accountable' => match ($this->accountable_type) {
        Patient::class => new PatientResource($this->whenLoaded('accountable')),
        Doctor::class => new DoctorResource($this->whenLoaded('accountable')),
        InsuranceSociety::class => new InsuranceSocietyResource($this->whenLoaded('accountable')),
        Employee::class => new EmployeeResource($this->whenLoaded('accountable')),
        default => null,
      },
    ];
  }
}
