<form method="get" action="{{ route('facilities.index') }}" role="search" aria-label="{{ __('facilities.search_label') }}">
    <div class="field">
        <label for="county">{{ __('facilities.county') }}</label>
        <select id="county" name="county">
            <option value="">{{ __('facilities.any_county') }}</option>
            @foreach (config('afyalink.counties') as $county)
                <option value="{{ $county }}" @selected(($filters['county'] ?? null) === $county)>{{ $county }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="type">{{ __('facilities.type') }}</label>
        <select id="type" name="type">
            <option value="">{{ __('facilities.any_type') }}</option>
            @foreach (\App\Models\Facility::TYPES as $type)
                <option value="{{ $type }}" @selected(($filters['type'] ?? null) === $type)>{{ __('facilities.types.'.$type) }}</option>
            @endforeach
        </select>
    </div>

    @if (isset($services) && $services->isNotEmpty())
        <div class="field">
            <label for="service">{{ __('facilities.service') }}</label>
            <select id="service" name="service">
                <option value="">{{ __('facilities.any_service') }}</option>
                @foreach ($services as $service)
                    <option value="{{ $service->slug }}" @selected(($filters['service'] ?? null) === $service->slug)>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="field">
        <label for="q">{{ __('facilities.name') }}</label>
        <input id="q" name="q" type="text" maxlength="100" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('facilities.name_placeholder') }}">
    </div>

    <button type="submit" class="button">{{ __('facilities.search_button') }}</button>
</form>
