<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;

class FileDownloadService
{
    public function generatePdf($pdfData, $viewFile, $fileName)
    {
        $pdf = new TCPDF;
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $chunkData = array_chunk($pdfData, 8);
        foreach ($chunkData as $row) {
            $data = ['aaData' => $row];
            $view = View::make($viewFile, $data);
            $html = $view->render();
            $pdf->AddPage('L', 'A4'); // Add new page when required
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        return response($pdf->Output($fileName, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename='$fileName'");
    }

    public function generateCsv($data, $columns, $fileName)
    {
        $callBack = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                $rowArray = (array) $row;
                fputcsv($file, array_values($rowArray));
            }
            fclose($file);
        };

        return response()->stream(
            $callBack,
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"\"$fileName",
            ]
        );
    }

    public function generateXlsx($data, $columns, $fileName)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([$columns], null, 'A1');

        $rowIndex = 2;
        foreach ($data as $row) {
            $rowArray = array_values((array) $row); // Convert to array and then use array_values
            $sheet->fromArray([$rowArray], null, 'A'.$rowIndex++);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
