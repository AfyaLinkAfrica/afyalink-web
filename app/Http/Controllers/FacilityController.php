<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FacilityController extends Controller
{
    private const PER_PAGE = 15;

    public function index(Request $request): View
    {
        $services = Service::query()->orderBy('name')->get(['id', 'name', 'slug']);
        $filters = $this->filters($request, $services);

        $facilities = Facility::query()
            ->with(['services' => fn ($query) => $query->orderBy('name')])
            ->inCounty($filters['county'])
            ->ofType($filters['type'])
            ->offeringService($filters['service'])
            ->nameLike($filters['q'])
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('facilities.index', [
            'facilities' => $facilities,
            'filters' => $filters,
            'services' => $services,
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
        $facility->load(['services' => fn ($query) => $query->orderBy('name')]);

        return view('facilities.show', ['facility' => $facility]);
    }

    /**
     * Read the search filters from the query string.
     * Unknown counties, types, or services, and non-text values, are ignored rather than rejected.
     *
     * @param  Collection<int, Service>  $services
     * @return array{county: ?string, type: ?string, service: ?string, q: ?string}
     */
    private function filters(Request $request, Collection $services): array
    {
        $county = $request->query('county');
        $type = $request->query('type');
        $service = $request->query('service');
        $term = $request->query('q');

        return [
            'county' => is_string($county) && in_array($county, config('afyalink.counties'), true) ? $county : null,
            'type' => is_string($type) && in_array($type, Facility::TYPES, true) ? $type : null,
            'service' => is_string($service) && $services->contains('slug', $service) ? $service : null,
            'q' => is_string($term) && trim($term) !== '' ? mb_substr(trim($term), 0, 100) : null,
        ];
    }
}
