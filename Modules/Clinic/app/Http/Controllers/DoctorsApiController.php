<?php

namespace Modules\Clinic\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Enum\PermissionNames;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Clinic\Http\Resources\DoctorResource;
use Modules\Clinic\Models\Doctor;
use Modules\Patients\Constants\CheckupStatus;
use Modules\Patients\Http\Resources\CheckupResource;
use Modules\Patients\Models\Checkup;
use Modules\Patients\Models\CheckupTicket;
use Modules\Transactions\Constants\Status;
use Modules\Transactions\Models\Transaction;
use Throwable;

class DoctorsApiController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::DOCTORS_VIEW);
    }

    try {
      $model = Doctor::whereHas('user.employee')->with(['user.employee', 'speciality']);

      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $model->whereHas('user.employee', function ($query) use ($searchTerm) {
          $query->where('fullname', 'like', "%{$searchTerm}%");
        });
      }

      if ($request->filled('created_at_from')) {
        $model->whereDate('created_at', '>=', $request->created_at_from);
      }

      if ($request->filled('created_at_to')) {
        $model->whereDate('created_at', '<=', $request->created_at_to);
      }

      if (filled($request->speciality_id)) {
        $model->where('speciality_id', $request->speciality_id);
      }

      $allowedSorts = ['id', 'created_at', 'speciality_id'];

      $model->when(
        in_array($request->get('sort_by'), $allowedSorts),
        fn($q) => $q->orderBy($request->get('sort_by'), $request->get('sort_order', 'desc')),
        fn($q) => $q->latest()
      );

     if ($request->boolean('paginate')) {
        $model = $model->paginate($request->get('per_page', 10));
      } else {
        $model = $model->get();
      }

      return $this->successResponse(
        data: DoctorResource::collection($model)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show(Doctor $doctor)
  {
    $this->authorizePermission(PermissionNames::DOCTORS_VIEW);

    try {
      $doctor->load(['user.employee', 'speciality']);

      return $this->successResponse(
        data: new DoctorResource($doctor)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function nextCheckup($doctorId)
  {
    $this->authorizePermission(PermissionNames::CHECKUPS_VIEW);

    try {
      $nextCheckup = Checkup::with(['ticket', 'patient'])
        ->where('doctor_id', $doctorId)
        ->where('date', '>=', now()->toDateString())
        ->where('status', CheckupStatus::PENDING)
        ->orderBy('date', 'asc')
        ->orderBy('time', 'asc')
        ->first();

      if (!$nextCheckup) {
        return $this->successResponse(message: 'No upcoming checkups found.');
      }

      return $this->successResponse(
        data: new CheckupResource($nextCheckup)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function checkupStats($doctorId)
  {
    $this->authorizePermission([
      PermissionNames::CHECKUPS_VIEW,
      PermissionNames::CHECKUPS_DOCTOR_VIEW
    ]);

    try {
      $doctor = Doctor::findOrFail($doctorId);
      $total = Checkup::where('doctor_id', $doctorId)->count();
      $byStatus = Checkup::where('doctor_id', $doctorId)
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();

      $tickets = CheckupTicket::doctorActiveTickets($doctorId)->count();

      $unpaidCheckupsCount = $doctor->transactions()
        ->where('status', Status::PENDING)
        ->count();

      $unpaidAmount = $doctor->transactions()
        ->where('status', Status::PENDING)
        ->sum('amount');

      $unpaidDayAmount = $doctor->transactions()
        ->where('status', Status::PENDING)
        ->whereDate('created_at', now()->toDateString())
        ->sum('amount');

      return $this->successResponse(
        data: [
          'total' => $total,
          'by_status' => $byStatus,
          'active_tickets_count' => $tickets,
          'unpaid_checkups_count' => $unpaidCheckupsCount,
          'unpaid_amount' => $unpaidAmount,
          'unpaid_today_amount' => $unpaidDayAmount,
        ]
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function statistics(Request $request)
  {
    try {
      $doctors = Doctor::with('user.employee')->get();
      $data = [];

      //all doctors statistics
      $totalEarnings = 0;
      $lastYearEarnings = 0;
      $lastMonthEarnings = 0;
      $lastWeekEarnings = 0;
      $todayEarnings = 0;

      //statistics per doctor
      foreach ($doctors as $doctor) {
        $totalEarningsPerDoctor = $doctor->transactions()
          ->where('status', Status::COMPLETED)
          ->sum('amount');

        $unpaidEarningsPerDoctor = $doctor->transactions()
          ->where('status', Status::PENDING)
          ->sum('amount');

        $lastYearEarningsPerDoctor = $doctor->transactions()
          ->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])
          ->where('status', Status::COMPLETED)
          ->sum('amount');

        $unpaidYearEarningsPerDoctor = $doctor->transactions()
          ->whereBetween('created_at', [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()])
          ->where('status', Status::PENDING)
          ->sum('amount');

        $lastMonthEarningsPerDoctor = $doctor->transactions()
          ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
          ->where('status', Status::COMPLETED)
          ->sum('amount');

        $unpaidMonthEarningsPerDoctor = $doctor->transactions()
          ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
          ->where('status', Status::PENDING)
          ->sum('amount');

        $lastWeekEarningsPerDoctor = $doctor->transactions()
          ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
          ->where('status', Status::COMPLETED)
          ->sum('amount');

        $unpaidWeekEarningsPerDoctor = $doctor->transactions()
          ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
          ->where('status', Status::PENDING)
          ->sum('amount');

        $todayEarningsPerDoctor = $doctor->transactions()
          ->whereDate('created_at', now()->toDateString())
          ->where('status', Status::COMPLETED)
          ->sum('amount');

        $unpaidTodayEarningsPerDoctor = $doctor->transactions()
          ->whereDate('created_at', now()->toDateString())
          ->where('status', Status::PENDING)
          ->sum('amount');

        $data[] = [
          'doctor' => new DoctorResource($doctor),
          'total_earnings' => $totalEarningsPerDoctor,
          'last_year_earnings' => $lastYearEarningsPerDoctor,
          'last_month_earnings' => $lastMonthEarningsPerDoctor,
          'last_week_earnings' => $lastWeekEarningsPerDoctor,
          'today_earnings' => $todayEarningsPerDoctor,
          'unpaid_earnings' => $unpaidEarningsPerDoctor,
          'unpaid_year_earnings' => $unpaidYearEarningsPerDoctor,
          'unpaid_month_earnings' => $unpaidMonthEarningsPerDoctor,
          'unpaid_week_earnings' => $unpaidWeekEarningsPerDoctor,
          'unpaid_today_earnings' => $unpaidTodayEarningsPerDoctor,
        ];

        $totalEarnings += $totalEarningsPerDoctor;
        $lastYearEarnings += $lastYearEarningsPerDoctor;
        $lastMonthEarnings += $lastMonthEarningsPerDoctor;
        $lastWeekEarnings += $lastWeekEarningsPerDoctor;
        $todayEarnings += $todayEarningsPerDoctor;
      }

      $data = [
        'overall_statistics' => [
          'total_earnings' => $totalEarnings,
          'last_year_earnings' => $lastYearEarnings,
          'last_month_earnings' => $lastMonthEarnings,
          'last_week_earnings' => $lastWeekEarnings,
          'today_earnings' => $todayEarnings,

        ],
        'per_doctor_statistics' => $data,
      ];

      return $this->successResponse(data: $data);
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }
}
