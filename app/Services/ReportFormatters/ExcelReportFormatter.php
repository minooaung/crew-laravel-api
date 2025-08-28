<?php

namespace App\Services\ReportFormatters;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

use App\Services\ReportFormatters\FieldRendererRegistry;

class ExcelReportFormatter implements ReportFormatterInterface
{
    public function format(string $reportType, array $columns, iterable $data): string
    {
        if (empty($data)) return '';

        // Initialize the field renderer registry
        // This registry will map field names to their respective renderers
        $fieldRendererRegistry = new FieldRendererRegistry();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Row
        $colIndex = 'A';
        foreach (array_keys($columns) as $header) {
            $sheet->setCellValue("{$colIndex}1", $header);
            $colIndex++;
        }

        // Data Rows
        $row = 2;
        foreach ($data as $item) {
            $colIndex = 'A';

            foreach ($columns as $label => $field) {
                // --- Following is commented out code that was previously used to handle specific fields like 'users'---
                // --- Now using the field renderer registry to handle all fields uniformly -----------------------------
                //
                // if ($field === 'users') {
                //     if ($item->users && count($item->users)) {
                //         $userLines = [];
                //         foreach ($item->users as $user) {
                //             $userLines[] = "{$user->name} ({$user->email}) \n";
                //         }
                //         $sheet->setCellValue("{$colIndex}{$row}", implode("\n", $userLines));
                //         $sheet->getStyle("{$colIndex}{$row}")->getAlignment()->setWrapText(true);
                //     } else {
                //         $sheet->setCellValue("{$colIndex}{$row}", "No assigned users");
                //     }
                // } else {
                //     $sheet->setCellValue("{$colIndex}{$row}", $item->{$field});
                // }
                // ---------------------------------------------------------------------------------------------------------
                
                // Use the field renderer to get the content for the cell
                // This allows for custom rendering logic for each field type
                $renderer = $fieldRendererRegistry->get($field);

                // Render the content and strip HTML tags
                // This ensures that the Excel output is clean and does not contain any HTML markup
                $content = strip_tags($renderer->render($item));

                // Set the cell value
                $sheet->setCellValue("{$colIndex}{$row}", $content);

                // Check if the content contains newlines and set wrap text if it does
                if (str_contains($content, "\n")) {
                    $sheet->getStyle("{$colIndex}{$row}")
                          ->getAlignment()
                          ->setWrapText(true);
                }

                $colIndex++;
            }
            $row++;
        }

        ob_start();
        $writer = new Xls($spreadsheet);
        $writer->save('php://output');
        return ob_get_clean();
    }
}