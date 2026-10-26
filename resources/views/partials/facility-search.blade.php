@push('styles')
<style>
    .field { margin-bottom: 1rem; }
    label { display: block; margin-bottom: .25rem; font-weight: 600; }
    select, input[type="text"] {
        width: 100%;
        min-height: 2.75rem;
        padding: .5rem .75rem;
        font: inherit;
        color: var(--text);
        background: var(--bg);
        border: 2px solid var(--border);
        border-radius: .5rem;
    }
    select:focus-visible, input[type="text"]:focus-visible, button:focus-visible {
        outline: 3px solid var(--brand-dark);
        outline-offset: 2px;
    }
    button.button {
        min-height: 2.75rem;
        padding: .7rem 1.2rem;
        font: inherit;
        font-weight: 600;
        cursor: pointer;
        color: #fff;
        background: var(--brand);
        border: 2px solid var(--brand);
        border-radius: .5rem;
    }
</style>
@endpush

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

    <div class="field">
        <label for="q">{{ __('facilities.name') }}</label>
        <input id="q" name="q" type="text" maxlength="100" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('facilities.name_placeholder') }}">
    </div>

    <button type="submit" class="button">{{ __('facilities.search_button') }}</button>
</form>
