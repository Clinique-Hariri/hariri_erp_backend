<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Salary Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 12px; }
    .header { margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 18px; }
    .meta { margin: 10px 0; }
    .meta div { margin: 2px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
    th { background: #f7f7f7; }
    .section-title { font-weight: bold; margin-top: 16px; }
    .right { text-align: right; }
  </style>
</head>
<body>
  <?php
    use Modules\HRM\Constants\SalaryStatus;
    use Modules\HRM\Constants\DeductionType;

    $employee = $salary->employee;
    $period = $salary->month ? $salary->month->format('Y-m') : '';
    $statusResource = SalaryStatus::get_resource($salary->status);
  ?>

  <div class="header">
    <h1>{{ config('app.name') }} — Salary Report</h1>
    <div class="meta">
      <div><strong>Employee:</strong> {{ $employee?->fullname }} ({{ $employee?->employee_code }})</div>
      <div><strong>Period:</strong> {{ $period }}</div>
      <div><strong>Status:</strong> {{ $statusResource['name'] ?? $salary->status }}</div>
      @if($salary->pay_date)
        <div><strong>Pay Date:</strong> {{ $salary->pay_date->format('Y-m-d') }}</div>
      @endif
    </div>
  </div>

  <table>
    <tbody>
      <tr>
        <th>Basic Salary</th>
        <td class="right">{{ number_format((float)$salary->basic_salary, 2) }}</td>
      </tr>
      <tr>
        <th>Daily Wage</th>
        <td class="right">{{ number_format((float)$salary->daily_wage, 2) }}</td>
      </tr>
      <tr>
        <th>Work Days</th>
        <td class="right">{{ (int)$salary->work_days }}</td>
      </tr>
      <tr>
        <th>Absent Days</th>
        <td class="right">{{ (int)$salary->absent_days }}</td>
      </tr>
      <tr>
        <th>Total Bonuses</th>
        <td class="right">{{ number_format((float)$salary->total_bonuses, 2) }}</td>
      </tr>
      <tr>
        <th>Total Deductions</th>
        <td class="right">{{ number_format((float)$salary->total_deduction, 2) }}</td>
      </tr>
      <tr>
        <th>Net Salary</th>
        <td class="right"><strong>{{ number_format((float)$salary->net_salary, 2) }}</strong></td>
      </tr>
    </tbody>
  </table>

  <div class="section-title">Bonuses</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th class="right">Amount</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($salary->bonuses as $i => $bonus)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $bonus->name }}</td>
          <td class="right">{{ number_format((float)$bonus->amount, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="3">No bonuses</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="section-title">Deductions</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Type</th>
        <th>Details</th>
        <th class="right">Amount</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($salary->deductions as $i => $deduction)
        <?php
          $typeResource = DeductionType::get_resource($deduction->type);
          $details = $deduction->loanInstallment
            ? ('Installment #' . $deduction->loanInstallment->id)
            : '';
        ?>
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $typeResource['name'] ?? $deduction->type }}</td>
          <td>{{ $details }}</td>
          <td class="right">{{ number_format((float)$deduction->amount, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4">No deductions</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="section-title">Loans</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Deduction Date</th>
        <th>Total Installments</th>
        <th class="right">Amount</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($employee->loans as $i => $loan)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $loan->deduction_date ?? '' }}</td>
          <td>{{ $loan->total_installments }}</td>
          <td class="right">{{ number_format((float)$loan->amount, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4">No loans</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <p style="margin-top: 20px; font-size: 11px; color: #666;">
    Generated on {{ now()->format('Y-m-d H:i') }}
  </p>
</body>
</html>
