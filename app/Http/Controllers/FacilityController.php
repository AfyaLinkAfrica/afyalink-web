<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    private const PER_PAGE = 15;

    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $facilities = Facility::query()
            ->inCounty($filters['county'])
            ->ofType($filters['type'])
            ->nameLike($filters['q'])
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('facilities.index', [
            'facilities' => $facilities,
            'filters' => $filters,
            'hasFilters' => array_filter($filters) !== [],
            'heading' => $filters['county'] !== null
                ? __('facilities.in_county', ['county' => $filters['county']])
                : __('facilities.heading'),
        ]);
    }

    /**
     * Show one facility. Route-model binding returns a 404 for unknown ids.
     */
    public function show(Facility $facility): View
    {
        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Read the search filters from the query string.
     * Unknown counties or types, and non-text values, are ignored rather than rejected.
     *
     * @return array{county: ?string, type: ?string, q: ?string}
     */
    private function filters(Request $request): array
    {
        $county = $request->query('county');
        $type = $request->query('type');
        $term = $request->query('q');

        return [
            'county' => is_string($county) && in_array($county, config('afyalink.counties'), true) ? $county : null,
            'type' => is_string($type) && in_array($type, Facility::TYPES, true) ? $type : null,
            'q' => is_string($term) && trim($term) !== '' ? mb_substr(trim($term), 0, 100) : null,
        ];
    }
}
