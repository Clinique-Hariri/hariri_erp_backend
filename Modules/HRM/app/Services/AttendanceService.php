<?php

namespace Modules\HRM\Services;

use Exception;
use Carbon\Carbon;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use Modules\HRM\Models\Attendance;
use Modules\HRM\Constants\AttendanceStatus;

class AttendanceService
{
  const FULL_WORKDAY_SECONDS = 28800; // 8 hours
  //const MAX_SHIFT_HOURS = 16;
  //const MIN_SHIFT_HOURS = 4;


  /**
   * Create attendance record
   */
  public function create(array $data): Attendance
  {

    $employee = Employee::findOrFail($data['employee_id']);

    if (!$employee->hasContractAt($data['date'])) {
      throw new Exception('Employee does not have an active contract in this date');
    }

    if ($employee->attendances()->whereDate('date', $data['date'])->exists()) {
      throw new Exception('Attendance record already exists for this date');
    }

    if (isset($data['check_in_time'])) {
      $data['check_in_time'] = $this->getCheckInDateTime($data['check_in_time'], $data['date']);
    }
    if (isset($data['check_out_time'])) {
      $data['check_out_time'] = $this->getCheckOutDateTime($data['check_out_time'], $data['check_in_time']);
      $data['duration'] = $this->getDuration($data['check_in_time'], $data['check_out_time']);
      $data['status'] = $this->getStatus($data['duration']);
    }

    return Attendance::create($data);
  }

  /**
   * Update attendance record
   */
  public function update(Attendance $attendance, array $data): Attendance
  {
    $checkIn = $data['check_in_time'] ?? $attendance->check_in_time;
    $checkOut = $data['check_out_time'] ?? $attendance->check_out_time;
    $date = $attendance->date->toDateString();

    if ($checkIn) {
      $data['check_in_time'] = $this->getCheckInDateTime($checkIn, $date);
    }
    if ($checkOut) {
      $data['check_out_time'] = $this->getCheckOutDateTime($checkOut, $data['check_in_time']);
      $data['duration'] = $this->getDuration($data['check_in_time'], $data['check_out_time']);
    }
    if (isset($data['duration']) || isset($data['status'])) {
      $data['status'] = $data['status'] ?? $this->getStatus($data['duration']);
    }


    $attendance->update($data);
    return $attendance->fresh();
  }

  /**
   * Handle auto check-in/out
   */
  /**
   * Handle auto check-in/out
   */
  public function autoCheckInOut($employeeIdentifier, ?Carbon $checkTime = null)
  {

    // 1. Get employee
    $employee = $this->findEmployee($employeeIdentifier);

    $date = $checkTime->toDateString();
    $time = $checkTime->toTimeString();

    if (!$employee->hasContractAt($date)) {
      throw new Exception('Employee does not have an active contract in this date');
    }

    // 3. Find the most recent employee attendance with checkin and missing checkout
    $attendance = $employee->active_shift;

    if ($attendance) {
      $attendance = $this->update($attendance, ['check_out_time' => $checkTime]);
    } else {
      $attendance = $this->create([
        'employee_id' => $employee->id,
        'date' => $date,
        'check_in_time' => $this->getCheckInDateTime($time, $date),
      ]);
    }


    return $attendance;

  }


  /**
   * Get check-in datetime
   * If input is already a datetime object, return it as is
   * If input is a time string, parse it with the given date
   */
  private function getCheckInDateTime($checkIn, $date): Carbon
  {
    if ($checkIn instanceof \DateTime) {
      return Carbon::instance($checkIn);
    }

    return Carbon::parse("$date $checkIn");
  }

  /**
   * Get check-out datetime
   * If input is already a datetime object, return it as is
   * If input is a time string, parse it with the given date and determine if it should be same day or next day
   */
  private function getCheckOutDateTime($checkOut, Carbon $checkInDateTime = null): Carbon
  {
    if ($checkOut instanceof \DateTime) {
      return $checkOut;
    }

    if (!$checkInDateTime) {
      throw new Exception('Check-in time is required before check-out time');
    }

    $date = $checkInDateTime->toDateString();

    // Try same day
    $sameDayOut = Carbon::parse("$date $checkOut");
    $sameDayDuration = $checkInDateTime->diffInSeconds($sameDayOut);

    // Try next day
    $nextDayOut = Carbon::parse("$date $checkOut")->addDay();
    $nextDayDuration = $checkInDateTime->diffInSeconds($nextDayOut);

    // Return the one that gives a positive and reasonable duration
    if ($sameDayDuration > 0 && $nextDayDuration > 0) {
      return $sameDayDuration <= $nextDayDuration ? $sameDayOut : $nextDayOut;
    } else {
      return $sameDayDuration > 0 ? $sameDayOut : $nextDayOut;
    }
  }

  /**
   * Calculate duration between check-in and check-out
   */
  public function getDuration($checkInDateTime, $checkOutDateTime)
  {
    $duration = $checkInDateTime->diffInSeconds($checkOutDateTime, false);

    /* if($duration >= (self::MAX_SHIFT_HOURS * 3600) || $duration <= (self::MIN_SHIFT_HOURS * 3600)){
      throw new Exception('Duration must be between ' . self::MIN_SHIFT_HOURS . ' and ' . self::MAX_SHIFT_HOURS . ' hours');
    } */

    return $duration;
  }

  /**
   * Get attendance status based on duration
   */
  public function getStatus(int $duration): string
  {
    return $duration >= self::FULL_WORKDAY_SECONDS
      ? AttendanceStatus::PRESENT
      : AttendanceStatus::LATE;
  }

  /**
   * Find employee by ID or code
   */
  private function findEmployee($identifier): Employee
  {
    $employee = is_numeric($identifier)
      ? Employee::find($identifier)
      : Employee::where('employee_code', $identifier)->first();

    if (!$employee) {
      throw new Exception('Employee not found');
    }

    return $employee;
  }
}
