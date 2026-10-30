<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const FILTERS = [Report::STATUS_NEW, Report::STATUS_RESOLVED, 'all'];

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $status = is_string($status) && in_array($status, self::FILTERS, true)
            ? $status
            : Report::STATUS_NEW;

        // New reports are a work queue (oldest first). Other views show the latest first.
        $direction = $status === Report::STATUS_NEW ? 'asc' : 'desc';

        $reports = Report::query()
            ->with('facility:id,name')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderBy('created_at', $direction)
            ->orderBy('id', $direction)
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'status' => $status,
            'newCount' => Report::query()->where('status', Report::STATUS_NEW)->count(),
        ]);
    }

    public function show(Report $report): View
    {
        return view('admin.reports.show', ['report' => $report->load('facility')]);
    }

    public function resolve(Report $report): RedirectResponse
    {
        // Resolving twice must not restart the 90-day retention clock.
        if ($report->status === Report::STATUS_RESOLVED) {
            return redirect()
                ->route('admin.reports.show', $report)
                ->with('admin_status', __('admin.already_resolved'));
        }

        $report->update([
            'status' => Report::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);

        return redirect()
            ->route('admin.reports.index')
            ->with('admin_status', __('admin.resolved_ok'));
    }
}
