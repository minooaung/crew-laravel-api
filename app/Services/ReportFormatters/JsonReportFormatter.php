<?php

namespace App\Services\ReportFormatters;

//use App\Services\ReportFormatters\ReportFormatterInterface;

class JsonReportFormatter implements ReportFormatterInterface
{
    public function format(string $reportType, array $columns, iterable $data): mixed
    {
        if (empty($data)) {
            return [];
        }

        // Initialize the field renderer registry
        // This registry will map field names to their respective renderers
        $fieldRendererRegistry = new FieldRendererRegistry();
        
        $result = [];

        foreach ($data as $item) {
            $row = [];

            foreach ($columns as $label => $field) {
                // --- Following is commented out code that was previously used to handle specific fields like 'users'---
                // --- Now using the field renderer registry to handle all fields uniformly -----------------------------
                //
                // if ($field === 'users' && method_exists($item, 'users')) {
                //     // Format assigned users
                //     $row[$label] = $item->users->map(function ($user) {
                //         return [
                //             'Name' => $user->name,
                //             'Email' => $user->email,
                //             'Role' => $user->role,
                //             'Created At' => $user->formatted_created_at ?? $user->created_at->format('Y-m-d H:i'),
                //         ];
                //     })->toArray();
                // } else {
                //     $row[$label] = $item->{$field};
                // }
                // ---------------------------------------------------------------------------------------------------------

                // Custom logic for 'users' field to print out users in proper json format
                if ($field === 'users' && $item->users && count($item->users)) {
                    $row[$label] = collect($item->users)->map(function ($user) {
                        return [
                            'Name' => $user->name,
                            'Email' => $user->email,
                            'Role' => $user->role,
                            'Created At' => $user->formatted_created_at ?? $user->created_at->format('Y-m-d H:i'),
                        ];
                    })->toArray();
                } else {
                    // Fallback to default text from renderer                    
                    $renderer = $fieldRendererRegistry->get($field);
                    $html = $renderer->render($item);
                    $row[$label] = strip_tags($html);
                }
            }

            $result[] = $row;
        }

        return $result;
    }
}