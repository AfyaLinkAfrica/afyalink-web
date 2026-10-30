@extends('layouts.app')

@section('title', __('admin.report_title', ['facility' => $report->facility->name]))

@push('styles')
<style>
    .back { display: inline-block; margin-bottom: 1rem; font-weight: 600; }
    dl.details { display: grid; grid-template-columns: max-content 1fr; gap: .5rem 1.5rem; margin: 1rem 0 0; }
    dl.details dt { font-weight: 700; }
    dl.details dd { margin: 0; color: var(--muted); }
    .report-message { margin: .25rem 0 0; white-space: pre-wrap; overflow-wrap: anywhere; color: var(--text); }
    @media (max-width: 30rem) {
        dl.details { grid-template-columns: 1fr; gap: 0; }
        dl.details dd { margin-bottom: .75rem; }
    }
</style>
@endpush

@section('content')
    <a class="back" href="{{ route('admin.reports.index') }}"><span aria-hidden="true">&larr;</span> {{ __('admin.back') }}</a>

    @if (session('admin_status'))
        <p class="notice success" role="status">{{ session('admin_status') }}</p>
    @endif

    <article class="card">
        <h1>{{ __('admin.report_title', ['facility' => $report->facility->name]) }}</h1>

        <dl class="details">
            <dt>{{ __('admin.facility') }}</dt>
            <dd><a href="{{ route('facilities.show', $report->facility) }}">{{ __('admin.view_facility') }}</a></dd>

            <dt>{{ __('admin.status') }}</dt>
            <dd>{{ $report->status === 'new' ? __('admin.status_new') : __('admin.status_resolved') }}</dd>

            <dt>{{ __('admin.received_label') }}</dt>
            <dd>{{ $report->created_at->format('j F Y, H:i') }}</dd>

            @if ($report->resolved_at)
                <dt>{{ __('admin.resolved_label') }}</dt>
                <dd>{{ $report->resolved_at->format('j F Y, H:i') }}</dd>
            @endif

            <dt>{{ __('admin.contact') }}</dt>
            <dd>
                {{ $report->contact ?? __('admin.contact_none') }}
                @if ($report->contact)
                    <span class="help">{{ __('admin.contact_note') }}</span>
                @endif
            </dd>

            <dt>{{ __('admin.message') }}</dt>
            <dd><p class="report-message">{{ $report->message }}</p></dd>
        </dl>

        @if ($report->status === 'new')
            <form method="POST" action="{{ route('admin.reports.resolve', $report) }}" class="actions">
                @csrf
                <button type="submit" class="button">{{ __('admin.resolve') }}</button>
            </form>
            <p class="help">{{ __('admin.resolve_note') }}</p>
        @endif
    </article>
@endsection
