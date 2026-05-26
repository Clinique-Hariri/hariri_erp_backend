<?php
// Blade view to render a printable summary table of salaries
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Salaries Summary' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f5f5f5; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Salaries Summary' }}</h1>
    <p class="muted">Generated at: {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Employee Code</th>
            <th>Employee Name</th>
            <th>Month</th>
            <th class="text-right">Basic Salary</th>
            <th class="text-right">Work Days</th>
            <th class="text-right">Absent Days</th>
            <th class="text-right">Total Bonuses</th>
            <th class="text-right">Total Deduction</th>
            <th class="text-right">Net Salary</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($salaries as $i => $salary)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ optional($salary->employee)->employee_code }}</td>
                <td>{{ optional($salary->employee)->fullname }}</td>
                <td>{{ \Carbon\Carbon::parse($salary->month)->format('Y-m') }}</td>
                <td class="text-right">{{ number_format((float)$salary->basic_salary, 2) }}</td>
                <td class="text-right">{{ (int)$salary->work_days }}</td>
                <td class="text-right">{{ (int)$salary->absent_days }}</td>
                <td class="text-right">{{ number_format((float)$salary->total_bonuses, 2) }}</td>
                <td class="text-right">{{ number_format((float)$salary->total_deduction, 2) }}</td>
                <td class="text-right">{{ number_format((float)$salary->net_salary, 2) }}</td>
                <td>{{ $salary->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center">No data for selected filters.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>