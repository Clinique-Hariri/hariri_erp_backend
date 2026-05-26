<?php

namespace Modules\HRM\Services;

use Dompdf\Options;
use Modules\HRM\Models\Salary;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class SalaryReportService
{
    /**
     * Generate a PDF report for the given salary and attach it to Salary::REPORT media collection.
     * Returns the public URL of the generated PDF.
     */
    public function generate(Salary $salary): string
    {
        // Ensure required relations are available
        $salary->loadMissing(['employee.loans', 'bonuses.bonus', 'deductions.loanInstallment']);

        // Render report HTML using a Blade view from HRM module
        $html = View::make('hrm::salaries.report', [
            'salary' => $salary,
        ])->render();

        // Use mPDF instead of Dompdf
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
            // 'tempDir' => storage_path('app/tmp'), // uncomment if you face temp dir restrictions
        ]);

        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');

        // Build a nice filename
        $period = $salary->month ? $salary->month->format('Y-m') : now()->format('Y-m');
        $employeeCode = $salary->employee->employee_code ?? 'employee';
        $fileName = "salary-{$employeeCode}-{$period}.pdf";

        // Save to a temporary path then attach to media library
        $tempDir = storage_path('app/tmp');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

        file_put_contents($tempPath, $pdfContent);

        // Attach to media library collection
        $media = $salary->addMedia($tempPath)
            ->usingFileName($fileName)
            ->toMediaCollection(Salary::REPORT);

        // Clean up temporary file
        @unlink($tempPath);

        // Return the public URL (works with your SalaryResource 'report' field)
        return $media->getUrl();
    }

    public function generateSummary($salaries, ?string $title = null): array
    {
        // Render table HTML using a Blade view
        $html = View::make('hrm::salaries.summary', [
            'title' => $title ?: 'Salaries Summary',
            'salaries' => $salaries,
        ])->render();

        // Use mPDF to generate a PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');

        $filename = 'salaries-summary-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return [$filename, $pdfContent];
    }
}
