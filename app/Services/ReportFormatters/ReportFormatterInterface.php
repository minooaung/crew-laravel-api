<?php

namespace App\Services\ReportFormatters;

interface ReportFormatterInterface
{
    public function format(string $reportType, array $columns, array|\Traversable $data): mixed;
}
