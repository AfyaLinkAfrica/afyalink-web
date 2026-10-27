<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function create(Facility $facility): View
    {
        return view('reports.create', ['facility' => $facility]);
    }

    public function store(StoreReportRequest $request, Facility $facility): RedirectResponse
    {
        // Honeypot: a filled hidden field means a bot. Pretend it worked and store nothing.
        if ($request->filled('hp_check')) {
            return $this->thanks($facility);
        }

        $facility->reports()->create($request->safe()->only(['message', 'contact']));

        return $this->thanks($facility);
    }

    private function thanks(Facility $facility): RedirectResponse
    {
        return redirect()
            ->route('facilities.show', $facility)
            ->with('report_submitted', true);
    }
}
