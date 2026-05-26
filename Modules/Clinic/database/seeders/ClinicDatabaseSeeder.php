<?php

namespace Modules\Clinic\Database\Seeders;

use App\Constants\Gender;
use App\Models\User;
use App\Support\Enum\UserRoles;
use App\Support\Enum\UserTypes;
use App\Support\Enum\WeekDays;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\Speciality;
use Modules\HRM\Models\Employee;

class ClinicDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      $names = [
        'طب القلب',
        'طب الأطفال',
        'الجراحة العامة',
        'طب النساء والولادة',
        'طب العيون',
        'طب الأسنان',
        'طب الجلدية',
        'طب الأنف والأذن والحنجرة',
        'طب العظام',
        'الطب النفسي',
        'طب الأعصاب',
        'طب المسالك البولية'
      ];

      Speciality::insert(array_map(function ($name) {
        return [
          'name' => $name,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }, $names));

//      //doctor
//      $doctorUser = User::create([
//        'fullname' => 'Doctor',
//        'email' => 'doctor@doctor.com',
//        'phone' => '0666666667',
//        'password' => bcrypt('doctor123'),
//        'type' => UserTypes::CLINIC,
//        'gender' => Gender::MALE,
//        'birthdate' => '1990-01-01',
//        'full_address' => 'Algiers, Algeria',
//        'status' => 'active',
//      ]);
//      $doctorUser->assignRole(UserRoles::DOCTOR);
//      $employee = Employee::create([
//        'user_id' => $doctorUser->id,
//        'employee_code' => uniqid(),
//        'fullname' => "Doctor",
//        'phone' => '066' . random_int(1000000, 9999999),
//        'email' => "doctor@example.com",
//        'hire_date' => '2025-07-01',
//      ]);
//
//      $doctor = Doctor::create([
//        'user_id' => $doctorUser->id,
//        'checkup_price' => 2000,
//        'speciality_id' => Speciality::inRandomOrder()->value('id'),
//        'commission_percentage' => 30,
//      ]);
//
//      //shcedule and working periods see ScheduleSeeder
//      foreach (WeekDays::lists() as $day) {
//        if ($day !== WeekDays::FRIDAY) {
//          $schedules = $doctor->schedules()->create([
//            'day_of_week' => $day,
//          ]);
//
//          //add working periods
//          $schedules->workingPeriods()->create([
//            'start_time' => Carbon::parse('08:00:00')->toTimeString(),
//            'end_time' => Carbon::parse('12:00:00')->toTimeString(),
//          ]);
//          $schedules->workingPeriods()->create([
//            'start_time' => Carbon::parse('13:00:00')->toTimeString(),
//            'end_time' => Carbon::parse('17:00:00')->toTimeString(),
//          ]);
//        }
//      }
    }
  }
}
