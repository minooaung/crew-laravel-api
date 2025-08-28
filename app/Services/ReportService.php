<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organisation;
use App\Services\ReportFormatters\ReportFormatterInterface;
use App\Services\ReportFormatters\ReportFormatterFactory;

class ReportService
{
    protected ReportFormatterFactory $formatterFactory;

    public function __construct(ReportFormatterFactory $formatterFactory)
    {
        $this->formatterFactory = $formatterFactory;
    }

    protected array $columnsMap = [
        'users' => [
            'Name' => 'name',
            'Email' => 'email',
            'Role' => 'role',
            'Created At' => 'formatted_created_at',
        ],
        'organisations' => [
            'Name' => 'name',
            'Created At' => 'formatted_created_at',
            'Assigned Users' => 'users', // This will be handled by the formatter
        ],
    ];

    public function generate(string $type, string $format): mixed
    {
        if (!array_key_exists($type, $this->columnsMap)) {
            throw new \InvalidArgumentException("Invalid report type: {$type}");
        }

        $data = match ($type) {
            'users' => User::all(),
            'organisations' => Organisation::with(['users'])->get(), // Fetch organisations with users
        };        

        $formatter = $this->formatterFactory->make($format);
        return $formatter->format($type, $this->columnsMap[$type], $data);
    }

    public function formatMetadata(string $format): array
    {
        return match ($format) {
            'pdf' => ['ext' => 'pdf', 'mime' => 'application/pdf'],
            'excel' => ['ext' => 'xls', 'mime' => 'application/vnd.ms-excel'],
            'csv' => ['ext' => 'csv', 'mime' => 'text/csv'],
            'json' => ['ext' => 'json', 'mime' => 'application/json'],
            'html' => ['ext' => 'html', 'mime' => 'text/html'],
            default => throw new \InvalidArgumentException("Unknown format: {$format}")
        };
    }
}
