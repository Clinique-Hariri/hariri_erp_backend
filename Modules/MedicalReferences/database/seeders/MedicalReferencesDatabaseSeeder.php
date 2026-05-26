<?php

namespace Modules\MedicalReferences\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MedicalReferences\Constants\MedicalServiceTypes;
use Modules\MedicalReferences\Models\ChronicDisease;
use Modules\MedicalReferences\Models\MedicalService;
use Modules\MedicalReferences\Models\MedicalServiceGroup;
use Modules\MedicalReferences\Models\Medicine;

class MedicalReferencesDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      $groups = [
        ['name' => 'blood'],
        ['name' => 'urine'],
        ['name' => 'imaging']
      ];
      $services = [
        [
          'name' => 'Blood Test',
          'type' => MedicalServiceTypes::ANALYSIS,
          'group_id' => 1,
          'price' => 50.00,
        ],
        [
          'name' => 'X-Ray',
          'type' => MedicalServiceTypes::RADIOLOGY,
          'group_id' => 3,
          'price' => 100.00,
        ],
        [
          'name' => 'MRI Scan',
          'type' => MedicalServiceTypes::RADIOLOGY,
          'group_id' => 3,
          'price' => 500.00,
        ],
        [
          'name' => 'Urine Analysis',
          'type' => MedicalServiceTypes::ANALYSIS,
          'group_id' => 2,
          'price' => 30.00,
        ],
        [
          'name' => 'CT Scan',
          'type' => MedicalServiceTypes::RADIOLOGY,
          'group_id' => 3,
          'price' => 400.00,
        ]
      ];

      foreach ($groups as $group) {
        MedicalServiceGroup::create($group);
      }

      foreach ($services as $service) {
        MedicalService::create($service);
      }


      //medicines seeder
      $medicines = [
        ['name' => 'Aspirin'],
        ['name' => 'Amoxicillin'],
        ['name' => 'Lisinopril'],
        ['name' => 'Metformin'],
        ['name' => 'Atorvastatin'],
      ];

      foreach ($medicines as $medicine) {
        Medicine::create($medicine);
      }

      //Chronic Diseases Seeder
      $chronicDiseases = [
        ['name' => 'Diabetes'],
        ['name' => 'Hypertension'],
        ['name' => 'Asthma'],
        ['name' => 'Chronic Obstructive Pulmonary Disease (COPD)'],
        ['name' => 'Arthritis'],
      ];

      foreach ($chronicDiseases as $disease) {
        ChronicDisease::create($disease);
      }
    }
  }
}
