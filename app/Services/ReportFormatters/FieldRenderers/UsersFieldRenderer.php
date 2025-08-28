<?php

namespace App\Services\ReportFormatters\FieldRenderers;

class UsersFieldRenderer implements FieldRendererInterface
{
    public function render(object $item): string
    {
        if ($item->users && count($item->users)) {
            $html = "<ul style='margin:0; padding-left:1.2em;'>";
            foreach ($item->users as $key => $user) {
                $html .= "<li>" . ($key + 1) . " - {$user->name} ({$user->email})  </li>";
            }
            $html .= "</ul>";
        } else {
            $html = "<em>No assigned users</em>";
        }

        return $html;
    }
}
