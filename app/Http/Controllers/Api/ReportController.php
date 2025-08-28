<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'reportType' => 'required|in:users,organisations',
            'outputFormat' => 'required|in:pdf,excel,csv,json,html',
        ]);

        $content = $this->reportService->generate(
            $validated['reportType'],
            $validated['outputFormat']
        );

        // JSON is returned as a standard API response
        if ($validated['outputFormat'] === 'json') {
            return response()->json($content);
        }

        // HTML is returned as a standard response
        if ($validated['outputFormat'] === 'html') {
            return response($content, 200)->header('Content-Type', 'text/html');
        }

        $meta = $this->reportService->formatMetadata($validated['outputFormat']);
        $filename = "{$validated['reportType']}-report.{$meta['ext']}";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => $meta['mime']]);
    }
}