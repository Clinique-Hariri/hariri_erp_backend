<?php

use Illuminate\Support\Facades\Route;
use Modules\HRM\app\Http\Controllers\Loan\LoanController;
use Modules\HRM\app\Http\Controllers\Bonus\BonusController;
use Modules\HRM\app\Http\Controllers\Salary\SalaryController;
use Modules\HRM\app\Http\Controllers\Loan\LoanMultiController;
use Modules\HRM\app\Http\Controllers\Bonus\BonusMultiController;
use Modules\HRM\app\Http\Controllers\Employee\EmployeeController;
use Modules\HRM\Http\Controllers\Department\DepartmentsController;
use Modules\HRM\app\Http\Controllers\Attendance\AttendanceController;
use Modules\HRM\app\Http\Controllers\Statistics\StatisticsController;
use Modules\HRM\app\Http\Controllers\Employee\EmployeeMultiController;
use Modules\HRM\app\Http\Controllers\Designation\DesignationController;
use Modules\HRM\app\Http\Controllers\CareerChange\CareerChangeController;
use Modules\HRM\app\Http\Controllers\Attendance\AttendanceMultiController;
use Modules\HRM\app\Http\Controllers\Designation\DesignationMultiController;
use Modules\HRM\app\Http\Controllers\CareerChange\CareerChangeMultiController;
use Modules\HRM\app\Http\Controllers\EmployeeBonus\EmployeeBonusMultiController;
use Modules\HRM\app\Http\Controllers\EmployeeContract\EmployeeContractController;
use Modules\HRM\app\Http\Controllers\EmployeeAttendance\EmployeeAttendanceController;
use Modules\HRM\app\Http\Controllers\Salary\SalaryMultiController;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
  Route::apiResource('designations', DesignationController::class);
  Route::apiResource('departments', DepartmentsController::class);
  Route::apiResource('bonuses', BonusController::class);
  Route::apiResource('employees', EmployeeController::class);
  Route::apiResource('attendances', AttendanceController::class);
  Route::apiResource('career-changes', CareerChangeController::class);
  Route::apiResource('loans', LoanController::class);
  Route::apiResource('salaries', SalaryController::class);

  Route::get('employee-statistics', [StatisticsController::class, 'employeeStatistics']);
  Route::get('employee-contracts/{employee_id}', [EmployeeContractController::class, 'index']);
  Route::get('employee-attendances/{employee_id}', [EmployeeAttendanceController::class, 'index']);
  Route::post('employee-check-in-out/{employee}', [AttendanceController::class, 'autoCheckInOut']);

  Route::post('employees-multi-delete', [EmployeeMultiController::class, 'destroy']);
  Route::post('designations-multi-delete', [DesignationMultiController::class, 'destroy']);
  Route::post('career-changes-multi-delete', [CareerChangeMultiController::class, 'destroy']);
  Route::post('bonuses-multi-delete', [BonusMultiController::class, 'destroy']);
  Route::post('attendances-multi-delete', [AttendanceMultiController::class, 'destroy']);
  Route::post('loans-multi-delete', [LoanMultiController::class, 'destroy']);
  Route::post('salaries-multi-delete', [SalaryMultiController::class, 'destroy']);

  Route::post('employee-bonuses-multi-delete', [EmployeeBonusMultiController::class, 'destroy']);
  Route::post('employee-bonuses-multi-create', [EmployeeBonusMultiController::class, 'store']);

  Route::post('attendances-multi-update-status', [AttendanceMultiController::class, 'updateStatus']);
  Route::post('attendances-multi-check-in', [AttendanceMultiController::class, 'multiCheckIn']);
  Route::post('attendances-multi-check-out', [AttendanceMultiController::class, 'multiCheckOut']);
  Route::put('salaries/{salary}/status', [SalaryController::class, 'updateStatus']);

  Route::post('salaries-generate', [SalaryController::class, 'generate']);
  Route::post('salaries-get-report/{salary}', [SalaryController::class, 'generateReport']);
  Route::post('salaries-get-reports', [SalaryMultiController::class, 'generateReports']);
  Route::post('salaries-get-summary', [SalaryMultiController::class, 'generateSummary']);
});
