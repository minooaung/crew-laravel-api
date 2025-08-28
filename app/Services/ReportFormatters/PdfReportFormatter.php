<?php

namespace App\Services\ReportFormatters;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ReportFormatters\FieldRendererRegistry;

class PdfReportFormatter implements ReportFormatterInterface
{
    public function format(string $reportType, array $columns, iterable $data): string
    {
        if (empty($data)) {
            return Pdf::loadHtml("<h1>No data available</h1>")->output();
        }

        // Initialize the field renderer registry
        // This registry will map field names to their respective renderers
        $fieldRendererRegistry = new FieldRendererRegistry();

        $html = "<h1>Report - {$reportType}</h1>";
        $html .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        $html .= "<tr>";

        foreach (array_keys($columns) as $header) {
            $html .= "<th style='background-color:#f2f2f2;'>{$header}</th>";
        }
        $html .= "</tr>";

        foreach ($data as $item) {
            $html .= "<tr>";

            foreach ($columns as $label => $field) {
                // --- Following is commented out code that was previously used to handle specific fields like 'users'---
                // --- Now using the field renderer registry to handle all fields uniformly -----------------------------
                //
                // if ($field === 'users') {
                //     $html .= "<td>";
                //     if ($item->users && count($item->users)) {
                //         $html .= "<ul style='margin:0; padding-left:1.2em;'>";
                //         foreach ($item->users as $user) {
                //             $html .= "<li>{$user->name} ({$user->email})</li>";
                //         }
                //         $html .= "</ul>";
                //     } else {
                //         $html .= "<em>No assigned users</em>";
                //     }
                //     $html .= "</td>";
                // } else {
                //     $html .= "<td>{$item->{$field}}</td>";
                // }
                // ---------------------------------------------------------------------------------------------------------
                
                // Use the field renderer to get the content for the cell
                // This allows for custom rendering logic for each field type
                $html .= "<td>" . $fieldRendererRegistry->get($field)->render($item) . "</td>";
            }

            $html .= "</tr>";
        }

        $html .= "</table>";

        return Pdf::loadHtml($html)->output();
    }
}