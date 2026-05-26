<?php

namespace Modules\HRM\Database\Seeders;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Modules\HRM\Models\Attendance;
use Modules\HRM\Models\Employee;
use Modules\HRM\Constants\AttendanceStatus;

class AttendanceSeeder extends Seeder
{
  public function run(): void
  {
    if (env('APP_ENV') === 'local') {
      // Target month
      $start = Carbon::parse('2025-07-01');
      $end = Carbon::parse('2025-07-31');
      $period = CarbonPeriod::create($start, $end);

      // Pick up to 5 employees with an active contract in July 2025
      // (mid-month date ensures the period falls inside)
      $employees = Employee::whereHasContractAt('2025-07-15')
        ->orderBy('id', 'asc')
        ->take(5)
        ->get();

      if ($employees->isEmpty()) {
        return;
      }

      foreach ($employees as $index => $employee) {
        $rows = [];
        foreach ($period as $date) {
          $day = (int)$date->format('j');

          // Scenarios assignment by employee index:
          // 0: present all month
          // 1: present most days (absent on 5,10,15,20,25,30)
          // 2: first half present (1..15), second half absent (16..31)
          // 3: absent most days (present only on 5,10,15,20,25,30)
          // 4+: absent all month
          $isPresent = match ($index) {
            0 => true,
            1 => $day % 5 !== 0,
            2 => $day <= 15,
            3 => $day % 5 === 0,
            default => false,
          };

          $row = [
            'employee_id' => $employee->id,
            'date' => $date->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
          ];

          if ($isPresent) {
            $checkIn = $date->copy()->setTime(9, 0, 0);
            $checkOut = $date->copy()->setTime(17, 0, 0);
            $row['check_in_time'] = $checkIn;
            $row['check_out_time'] = $checkOut;
            $row['duration'] = 8 * 60 * 60; // 8 hours in seconds
            $row['status'] = AttendanceStatus::PRESENT;
          } else {
            $row['check_in_time'] = null;
            $row['check_out_time'] = null;
            $row['duration'] = null;
            $row['status'] = AttendanceStatus::ABSENT;
          }

          $rows[] = $row;
        }

        // Use upsert to safely re-run seeder without duplicates
        Attendance::upsert(
          $rows,
          ['employee_id', 'date'],
          ['status', 'check_in_time', 'check_out_time', 'duration', 'updated_at']
        );
      }
    }
  }
}
