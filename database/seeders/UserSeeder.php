<?php

namespace Database\Seeders;

use App\Constants\Gender;
use App\Models\User;
use App\Support\Enum\UserRoles;
use App\Support\Enum\UserTypes;
use App\Support\Enum\WeekDays;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\Speciality;
use Modules\HRM\Models\Employee;
use Symfony\Component\HttpFoundation\File\File;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $filePath = storeWebP(new File(public_path('assets/img/avatars/1.png')), 'uploads/users/avatars');

    foreach (UserRoles::all() as $role) {
      $type = match ($role) {
        UserRoles::SUPER_ADMIN, UserRoles::ADMIN => UserTypes::ADMIN,
        UserRoles::RECEPTIONIST, UserRoles::DOCTOR, UserRoles::RADIOLOGY_MANAGER, UserRoles::LABORATORY_MANAGER, UserRoles::OPERATIONS_MANAGER, UserRoles::ACCOUNTANT => UserTypes::CLINIC,
        UserRoles::HUMAN_RESOURCES => UserTypes::HUMAN_RESOURCE,
        UserRoles::INVENTORY_MANAGER => UserTypes::INVENTORY,
        default => UserTypes::CLINIC,
      };

      $user = User::where('email', $role . '@' . $role . '.com')->first();

      if (!$user) {
        $user = User::create([
          'fullname' => ucfirst(str_replace('_', ' ', $role)),
          'email' => strtolower(str_replace('_', '', $role) . '@' . str_replace('_', '', $role) . '.com'),
          'phone' => '0666666' . rand(100, 999),
          'password' => bcrypt(strtolower(str_replace('_', '', $role) . '123')),
          'type' => $type,
          'gender' => Gender::MALE,
          'birthdate' => '1990-01-01',
          'full_address' => 'Algiers, Algeria',
          'status' => 'active',
        ]);
        $user->avatar = $filePath;
        $user->save();
        $user->assignRole($role);

        if (!$user->hasRole(UserRoles::SUPER_ADMIN) && !$user->hasRole(UserRoles::ADMIN)) {
          // Create associated employee record for non-admin users
          $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => uniqid(),
            'fullname' => $user->fullname,
            'phone' => $user->phone,
            'email' => $user->email,
            'hire_date' => now(),
          ]);

          if ($user->hasRole(UserRoles::DOCTOR)) {
            // Create a doctor profile if the user is a doctor
            $doctor = Doctor::create([
              'user_id' => $user->id,
              'checkup_price' => 2000,
              'speciality_id' => null,
              'commission_percentage' => 30,
            ]);

            // Create Shcedule for the doctor
            foreach (WeekDays::lists() as $day) {
              if ($day !== WeekDays::FRIDAY) {
                $schedules = $doctor->schedules()->create([
                  'day_of_week' => $day,
                ]);

                //add working periods
                $schedules->workingPeriods()->create([
                  'start_time' => Carbon::parse('08:00:00')->toTimeString(),
                  'end_time' => Carbon::parse('12:00:00')->toTimeString(),
                ]);
                $schedules->workingPeriods()->create([
                  'start_time' => Carbon::parse('13:00:00')->toTimeString(),
                  'end_time' => Carbon::parse('17:00:00')->toTimeString(),
                ]);
              }
            }
          }
        }
      }
    }

    // Check if admin user already exists
//    $adminUser = User::where('email', 'admin@admin.com')->first();
//    if (!$adminUser) {
//        $adminUser = User::create([
//            'fullname' => 'Super Admin',
//            'email' => 'admin@admin.com',
//            'phone' => '0666666666',
//            'password' => bcrypt('admin123'),
//            'type' => UserTypes::ADMIN,
//            'gender' => Gender::MALE,
//            'birthdate' => '1990-01-01',
//            'full_address' => 'Algiers, Algeria',
//            'status' => 'active',
//        ]);
//        $adminUser->avatar = $filePath;
//        $adminUser->save();
//        $adminUser->assignRole(UserRoles::SUPER_ADMIN);
//    }
  }
}
