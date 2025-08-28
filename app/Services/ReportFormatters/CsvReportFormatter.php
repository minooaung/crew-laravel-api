<?php

namespace App\Services\ReportFormatters;

use App\Services\ReportFormatters\FieldRendererRegistry;

class CsvReportFormatter implements ReportFormatterInterface
{
    public function format(string $reportType, array $columns, iterable $data): string
    {
        if (empty($data)) {
            return '';
        }

        // Initialize the field renderer registry
        // This registry will map field names to their respective renderers
        $fieldRendererRegistry = new FieldRendererRegistry();

        //$csv = implode(',', array_keys($columns)) . "\n";
        // Header row
        $csv = implode(',', array_map(fn($label) => "\"{$label}\"", array_keys($columns))) . "\n";

        foreach ($data as $item) {
            $row = [];

            foreach ($columns as $label => $field) {
                // --- Following is commented out code that was previously used to handle specific fields like 'users'---
                // --- Now using the field renderer registry to handle all fields uniformly -----------------------------
                //
                // if ($field === 'users' && method_exists($item, 'users')) {
                //     if ($item->users && count($item->users)) {
                //         $userSummary = collect($item->users)
                //             ->map(fn($user) => "{$user->name} ({$user->email})")
                //             ->implode(';  ');
                //         $row[] = "\"{$userSummary}\"";
                //     } else {
                //         $row[] = "\"No assigned users\"";
                //     }
                // } else {
                //     $row[] = "\"{$item->{$field}}\"";
                // }
                // ---------------------------------------------------------------------------------------------------------

                
                // Use the field renderer to get the content for the cell
                // This allows for custom rendering logic for each field type
                $renderer = $fieldRendererRegistry->get($field);

                // Render the content and strip HTML tags
                // This ensures that the CSV output is clean and does not contain any HTML markup
                $content = strip_tags($renderer->render($item));

                // Escape double quotes and wrap in quotes
                $escaped = str_replace('"', '""', $content);
                $row[] = "\"{$escaped}\"";
            }

            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }
}