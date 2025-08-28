<?php

namespace App\Services\ReportFormatters\FieldRenderers;

class DefaultFieldRenderer implements FieldRendererInterface
{
    protected string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function render(object $item): string
    {
        return e($item->{$this->field}); // `e()` escapes output in Laravel
    }
}
