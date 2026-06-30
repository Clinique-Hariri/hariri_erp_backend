<?php

namespace Modules\HRM\app\Http\Controllers\Employee;

use Illuminate\Database\QueryException;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\HRM\Models\Contract;
use Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Support\Enum\PermissionNames;
use Modules\Clinic\Models\Doctor;
use Modules\HRM\Http\Resources\EmployeeResource;
use Modules\HRM\Http\Requests\Employee\StoreEmployeeRequest;
use Modules\HRM\Http\Requests\Employee\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
  use ApiResponseTrait;

  public function index(Request $request)
  {
    if ($request->boolean('paginate')) {
      $this->authorizePermission(PermissionNames::EMPLOYEES_VIEW);
    }

    try {
      $query = Employee::with(['user', 'contracts']);

      // Apply search filter
      if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
          $q->where('fullname', 'like', "%{$searchTerm}%")
            ->orWhere('employee_code', 'like', "%{$searchTerm}%");
        });
      }
      // Filter by contract status
      if ($request->filled('contract_status')) {
        $query->FilterbyContractStatus($request->contract_status);
      }

      // Filter by designation
      if ($request->filled('designation_id')) {
        $query->whereHas('contract', function ($q) use ($request) {
          $q->where('designation_id', $request->designation_id);
        });
      }

      // Filter by department
      if ($request->filled('department_id')) {
        $query->whereHas('contract', function ($q) use ($request) {
          $q->where('department_id', $request->department_id);
        });
      }

      // Filter by hire date range
      if ($request->filled('hire_date_from')) {
        $query->whereDate('hire_date', '>=', $request->hire_date_from);
      }

      if ($request->filled('hire_date_to')) {
        $query->whereDate('hire_date', '<=', $request->hire_date_to);
      }

      if ($request->boolean('paginate')) {
        $employees = $query->paginate($request->get('per_page', 10));
      } else {
        $employees = $query->get();
      }

      return $this->successResponse(
        data: EmployeeResource::collection($employees)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function show($id)
  {
    $this->authorizePermission(PermissionNames::EMPLOYEES_VIEW);

    try {
      $employee = Employee::with(['user', 'contracts', 'user.insuranceSocieties'])->findOrFail($id);

      return $this->successResponse(
        data: new EmployeeResource($employee)
      );
    } catch (Throwable $e) {
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function store(StoreEmployeeRequest $request)
  {
    $this->authorizePermission(PermissionNames::EMPLOYEES_CREATE);

    $employeeData = $this->validateRequest($request, $request->rules());

    try {
      DB::beginTransaction();

      // Create user if requested
      if ($request->input('create_user', false)) {
        $userData = [
          'fullname' => $employeeData['fullname'],
          'email' => $employeeData['email'],
          'phone' => $employeeData['phone'],
          'password' => Hash::make($employeeData['password']),
          'gender' => $employeeData['gender'],
          'birthdate' => $employeeData['birth_date'],
          'full_address' => $employeeData['address'],
          'type' => $employeeData['user_type'],
          'status' => 1,
        ];

        $user = User::create($userData);

        if (isset($employeeData['user_role'])) {
          $user->assignRole($employeeData['user_role']);
        }

        if ($request->filled('insurance_society_ids')) {
          $user->insuranceSocieties()->sync($employeeData['insurance_society_ids']);
        }

        if ($request->input('create_doctor', false)) {
          Doctor::create([
            'user_id' => $user->id,
            'speciality_id' => $employeeData['speciality_id'],
            'checkup_price' => $employeeData['checkup_price'],
            'commission_percentages' => $employeeData['commission_percentages'] ?? [
              'checkup' => 0,
              'analysis' => 0,
              'hospitalization' => 0,
              'operation' => 0,
            ],
          ]);
        }
      }

      // Create employee
      $employee = Employee::create([
        'user_id' => isset($user) ? $user->id : null,
        'employee_code' => uniqid(),
        'fullname' => $employeeData['fullname'],
        'phone' => $employeeData['phone'],
        'email' => $employeeData['email'],
        'gender' => $employeeData['gender'],
        'address' => $employeeData['address'],
        'birth_date' => $employeeData['birth_date'],
        'hire_date' => $employeeData['hire_date'] ?? now(),
      ]);

      // Handle image upload
      if ($request->hasFile('image')) {
        storeWebPWithSpatie($employee, $request->file('image'), Employee::IMAGE);
      }

      // Create contract
      if ($request->input('create_contract', false)) {
        Contract::create([
          'employee_id' => $employee->id,
          'department_id' => $employeeData['department_id'],
          'designation_id' => $employeeData['designation_id'],
          'start_date' => Carbon::parse($employeeData['hire_date'])->startOfMonth(),
          'end_date' => Carbon::parse($employeeData['end_date'])->endOfMonth(),
          'basic_salary' => $employeeData['basic_salary'] ?? null
        ]);
      }

      DB::commit();

      return $this->successResponse(
        message: 'Employee created successfully',
        data: new EmployeeResource($employee->load(['user', 'contracts', 'user.insuranceSocieties']))
      );
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function update(UpdateEmployeeRequest $request, $id)
  {
    $this->authorizePermission(PermissionNames::EMPLOYEES_UPDATE);

    $employee = Employee::findOrFail($id);
    $employee->load('user.doctor');

    $employeeData = $this->validateRequest($request, $request->rules($employee));

    try {
      DB::beginTransaction();

      $employee->update($employeeData);

      // Handle image upload
      if ($request->hasFile('image')) {
        $employee->clearMediaCollection(Employee::IMAGE);
        storeWebPWithSpatie($employee, $request->file('image'), Employee::IMAGE);
      }

      // Create user if requested and employee doesn't have a user
      if ($request->input('create_user', false) && !$employee->user_id) {
        $userData = [
          'fullname' => $employee->fullname,
          'email' => $employee->email,
          'phone' => $employee->phone,
          'password' => Hash::make($employeeData['password']),
          'gender' => $employee->gender,
          'birthdate' => $employee->birthdate,
          'full_address' => $employee->full_address,
          'type' => $employeeData['user_type'],
        ];

        if ($request->filled('hire_date')) {
          $userData['hire_date'] = $employeeData['hire_date'];
        }

        $user = User::create($userData);

        if (isset($employeeData['user_role'])) {
          $user->assignRole($employeeData['user_role']);
        }

        if ($request->filled('insurance_society_ids')) {
          $user->insuranceSocieties()->sync($employeeData['insurance_society_ids']);
        }

        $employee->update(['user_id' => $user->id]);
      }

      if ($request->filled('speciality_id', 'checkup_price')) {
        $employee->doctor->update([
          'speciality_id' => $employeeData['speciality_id'],
          'checkup_price' => $employeeData['checkup_price'],
          'commission_percentages' => $employeeData['commission_percentages'],
        ]);
      }

      if ($request->filled('insurance_society_ids') && $employee->user_id) {
        $employee->user->insuranceSocieties()->sync($employeeData['insurance_society_ids']);
      }

      DB::commit();

      return $this->successResponse(
        message: 'Employee updated successfully',
        data: new EmployeeResource($employee->load(['user', 'contracts', 'user.insuranceSocieties']))
      );
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  public function destroy($id)
  {
    $this->authorizePermission(PermissionNames::EMPLOYEES_DELETE);

    try {
      $employee = Employee::with('user')->findOrFail($id);

      if ($employee->hasContractAt(now())) {
        return $this->errorResponse(__('messages.cannot_delete_employee_with_active_contract'), 400);
      }

      DB::beginTransaction();

      $this->deleteEmployeeRelations($employee);

      $employee->delete();

      DB::commit();

      return $this->successResponse(
        data: [
          'total' => Employee::count()
        ]
      );
    } catch (QueryException $e) {
      DB::rollBack();
      // MySQL foreign key violation code = 23000
      if ($e->getCode() == 23000) {
        return response()->json([
          'success' => false,
          'message' => __('messages.cannot_delete_record_linked_to_other_records')
        ], 400);
      }

      // Fallback for any other database error
      return response()->json([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
      ], 500);
    } catch (Throwable $e) {
      DB::rollBack();
      return $this->errorResponse($e->getMessage(), 500);
    }
  }

  private function deleteEmployeeRelations(Employee $employee): void
  {
    // Delete all related records
    $employee->contracts()->delete();
    $employee->employeeBonuses()->delete();
    $employee->careerChanges()->delete();
    $employee->attendances()->delete();
    $employee->loans()->delete();
    $employee->salaries()->delete();
    $employee->transactions()->delete();
    $employee->clearMediaCollection(Employee::IMAGE);

    // Delete the associated user if exists
    if ($employee->user) {
      $employee->user->delete();
    }
  }
}
