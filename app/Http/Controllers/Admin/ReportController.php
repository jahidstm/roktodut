<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $validStatuses = ['open', 'reviewing', 'resolved', 'dismissed'];

        $reports = Report::query()
            ->with(['reportable', 'reporter:id,name,email', 'resolver:id,name'])
            ->when(in_array($status, $validStatuses, true), fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'open' => Report::where('status', 'open')->count(),
            'reviewing' => Report::where('status', 'reviewing')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
            'total' => Report::count(),
        ];

        return view('admin.reports.index', compact('reports', 'status', 'counts'));
    }

    public function show(Report $report): View
    {
        $report->load(['reportable', 'reporter:id,name,email,role', 'resolver:id,name']);

        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $previousStatus = $report->status;

        $validated = $request->validate([
            'status' => ['required', 'in:open,reviewing,resolved,dismissed'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'status.required' => 'স্ট্যাটাস নির্বাচন করুন।',
            'status.in' => 'অবৈধ স্ট্যাটাস নির্বাচিত হয়েছে।',
        ]);

        $isResolved = in_array($validated['status'], ['resolved', 'dismissed'], true);

        $report->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
            'resolved_by' => $isResolved ? $request->user()?->id : null,
        ]);

        AuditLogger::log(
            action: 'admin.report.status_changed',
            target: $report,
            metadata: [
                'from_status' => $previousStatus,
                'to_status' => $validated['status'],
                'admin_note' => $validated['admin_note'] ?? null,
            ],
        );

        return back()->with('success', 'রিপোর্ট স্ট্যাটাস আপডেট হয়েছে।');
    }
}
