<?php

namespace App\Services\ReportFormatters;

use InvalidArgumentException;

class ReportFormatterFactory
{
    public function make(string $format): ReportFormatterInterface
    {
        return match ($format) {
            'pdf' => new PdfReportFormatter(),
            'excel' => new ExcelReportFormatter(),
            'csv' => new CsvReportFormatter(),
            'html' => new HtmlReportFormatter(),
            'json' => new JsonReportFormatter(),
            default => throw new InvalidArgumentException("Unsupported format: {$format}"),
        };
    }
}
