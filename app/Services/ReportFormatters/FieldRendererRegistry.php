<?php

namespace App\Services\ReportFormatters;

use App\Services\ReportFormatters\FieldRenderers\FieldRendererInterface;
use App\Services\ReportFormatters\FieldRenderers\DefaultFieldRenderer;
use App\Services\ReportFormatters\FieldRenderers\UsersFieldRenderer;

class FieldRendererRegistry
{
    protected array $renderers = [];

    protected array $defaultFieldMap = [
        'users' => UsersFieldRenderer::class,        
        // Add more mappings here like 'organisations' => OrganisationsFieldRenderer::class,
    ];

    public function __construct()
    {
        foreach ($this->defaultFieldMap as $field => $rendererClass) {
            $this->register($field, new $rendererClass());
        }
    }

    public function register(string $field, FieldRendererInterface $renderer): void
    {
        $this->renderers[$field] = $renderer;
    }

    public function get(string $field): FieldRendererInterface
    {
        return $this->renderers[$field] ?? new DefaultFieldRenderer($field);
    }
}
