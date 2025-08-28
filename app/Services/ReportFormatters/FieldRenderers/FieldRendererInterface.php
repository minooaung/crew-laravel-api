<?php

namespace App\Services\ReportFormatters\FieldRenderers;

interface FieldRendererInterface
{
    public function render(object $item): string;
}
