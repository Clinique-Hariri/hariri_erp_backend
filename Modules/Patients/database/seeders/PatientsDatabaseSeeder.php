<?php

namespace Modules\Patients\Database\Seeders;

use App\Constants\BloodType;
use App\Constants\Gender;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Clinic\Models\Doctor;
use Modules\MedicalReferences\Models\MedicalService;
use Modules\Patients\Constants\CheckupAnalysisStatus;
use Modules\Patients\Constants\CheckupStatus;
use Modules\Patients\Constants\HospitalizationStatus;
use Modules\Patients\Constants\OperationStatus;
use Modules\Patients\Constants\PatientStatus;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupTicket;
use Modules\Patients\Models\Hospitalization;
use Modules\Patients\Models\Operation;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\Surgeon;

class PatientsDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if(env('APP_ENV') === 'local') {
      DB::transaction(function () {
        // Create patients
        $patients = $this->createPatients(10);

        // Get doctors
        $doctors = Doctor::get();

        // Get medical services
        $medicalServices = MedicalService::all();

        // Create checkups for each patient
        foreach ($patients as $patient) {
          // Create 1-3 checkups for each patient
          $checkupsCount = rand(1, 3);

          for ($i = 0; $i < $checkupsCount; $i++) {
            // Select a random doctor
            $doctor = $doctors->random();

            // Create a checkup
            $checkup = Checkup::create([
              'patient_id' => $patient->id,
              'doctor_id' => $doctor->id,
              'checkup_number' => Checkup::generateUniqueCheckupNumber(),
              'initial_price' => rand(100, 500),
              'original_price' => rand(100, 500),
              'total_price' => 0, // Will be calculated after adding services
              'date' => now()->format('Y-m-d'),
              'time' => now()->format('H:i'),
              'status' => array_rand(CheckupStatus::all()),
              'reason' => 'Regular checkup',
            ]);

            // Create checkup ticket
            $ticket = $checkup->ticket()->create([
              'date' => $checkup->date,
            ]);
          }
        }

        // Create hospitalizations for some patients
        $this->createHospitalizations($patients);
        $this->createOperations($patients);

      });
    }
  }

  /**
   * Create sample patients
   */
  private function createPatients(int $count = 10)
  {
    $patients = [];
    $genders = array_keys(Gender::all());
    $bloodTypes = array_keys(BloodType::all());
    $statuses = array_keys(PatientStatus::all());

    for ($i = 1; $i <= $count; $i++) {
      $patient = Patient::create([
        'patient_number' => Patient::generateUniquePatientNumber(),
        'fullname' => 'Patient ' . $i,
        'gender' => $genders[array_rand($genders)],
        'blood_type' => $bloodTypes[array_rand($bloodTypes)],
        'birthdate' => now()->subYears(rand(18, 70))->format('Y-m-d'),
        'birth_place' => 'City ' . $i,
        'full_address' => 'Address ' . $i . ', Street ' . $i . ', City ' . $i,
        'phone' => '06' . rand(10000000, 99999999),
        'status' => $statuses[array_rand($statuses)],
      ]);

      $patients[] = $patient;
    }

    return $patients;
  }

  /**
   * Create sample hospitalizations
   */
  private function createHospitalizations($patients)
  {
    // Create hospitalizations for about half of the patients
    $patientsForHospitalization = collect($patients)->random(min(5, count($patients)));

    $rooms = ['101', '102', '103', '201', '202', '203', '301', '302', '303', '401'];
    $statuses = [
      HospitalizationStatus::DRAFT,
      HospitalizationStatus::ACCEPTED,
      HospitalizationStatus::ADMITTED,
      HospitalizationStatus::DISCHARGED
    ];

    foreach ($patientsForHospitalization as $patient) {
      // Create 1-2 hospitalizations per selected patient
      $hospitalizationsCount = rand(1, 2);

      for ($i = 0; $i < $hospitalizationsCount; $i++) {
        $status = $statuses[array_rand($statuses)];
        $admissionDate = now()->subDays(rand(1, 60));
        $initialPrice = rand(1000, 5000);

        $dischargeDate = null;
        $stayLength = 0; // 默认设置为 0 而不是 null
        $remainingAmount = $initialPrice;

        // Set discharge date and stay length for discharged patients
        if ($status === HospitalizationStatus::DISCHARGED) {
          $dischargeDate = $admissionDate->copy()->addDays(rand(1, 14));
          $stayLength = $admissionDate->diffInDays($dischargeDate);
          $remainingAmount = 0;
        } elseif ($status === HospitalizationStatus::ADMITTED) {
          // For admitted patients, calculate current stay length
          $stayLength = $admissionDate->diffInDays(now());
          $remainingAmount = rand(0, $initialPrice);
        } else {
          // For pending patients, stay length is 0
          $stayLength = 0;
        }

        Hospitalization::create([
          'patient_id' => $patient->id,
          'admission_date' => $admissionDate,
          'discharge_date' => $dischargeDate,
          'stay_length' => $stayLength,
          'room_number' => $rooms[array_rand($rooms)],
          'initial_price' => $initialPrice,
          'total_price' => $initialPrice,
          'status' => $status,
          'created_at' => $admissionDate,
          'updated_at' => $dischargeDate ?? $admissionDate,
        ]);
      }
    }

    $this->command->info('Created hospitalizations for ' . $patientsForHospitalization->count() . ' patients.');
  }

  // 在 createHospitalizations 方法之后添加以下方法
private function createOperations($patients)
{
    // Create operations for about half of the patients
    $patientsForOperations = collect($patients)->random(min(5, count($patients)));
    $doctors = Doctor::get();

    // Ensure we have enough doctors
    if ($doctors->count() === 0) {
        $this->command->warn('No clinic doctors found. Skipping operations seeding.');
        return;
    }

    foreach ($patientsForOperations as $patient) {
        // Create 1-2 operations per selected patient
        $operationsCount = rand(1, 2);

        for ($i = 0; $i < $operationsCount; $i++) {
            // Select random doctors for surgeons (1-3 surgeons per operation)
            $surgeonCount = min(rand(1, 3), $doctors->count());
            $selectedSurgeons = $doctors->random($surgeonCount);

            // Create operation
            $operation = Operation::create([
                'patient_id' => $patient->id,
                'operation_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'price' => rand(1000, 5000),
                'description' => 'Operation description for patient ' . $patient->id,
                'status' => array_rand(OperationStatus::all()),
            ]);

            // Create surgeons for this operation
            foreach ($selectedSurgeons as $surgeon) {
                Surgeon::create([
                    'operation_id' => $operation->id,
                    'doctor_id' => $surgeon->id,
                    'doctor_commission_percentage' => rand(5, 20),
                ]);
            }
        }
    }

    $this->command->info('Created operations for ' . $patientsForOperations->count() . ' patients.');
}



}
