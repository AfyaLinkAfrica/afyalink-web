@extends('layouts.app')

@section('title', __('admin.reports_title'))

@push('styles')
<style>
    .admin-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; margin-bottom: 1rem; }
    .admin-bar h1 { margin: 0; }
    .admin-tabs { margin-bottom: 1rem; }
    ul.reports { display: grid; gap: 1rem; margin: 0 0 1rem; padding: 0; list-style: none; }
    ul.reports .card { padding: 1rem 1.25rem; }
    ul.reports h2 { margin: 0 0 .25rem; font-size: 1.15rem; line-height: 1.3; }
    .pager { display: flex; justify-content: space-between; gap: 1rem; font-weight: 600; }
</style>
@endpush

@section('content')
    <div class="admin-bar">
        <h1>{{ __('admin.reports_title') }}</h1>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="button">{{ __('admin.sign_out') }}</button>
        </form>
    </div>

    @if (session('admin_status'))
        <p class="notice success" role="status">{{ session('admin_status') }}</p>
    @endif

    <nav class="admin-tabs" aria-label="{{ __('admin.filter_label') }}">
        <ul>
            <li><a href="{{ route('admin.reports.index', ['status' => 'new']) }}" @if ($status === 'new') aria-current="page" @endif>{{ __('admin.filter_new') }} ({{ $newCount }})</a></li>
            <li><a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" @if ($status === 'resolved') aria-current="page" @endif>{{ __('admin.filter_resolved') }}</a></li>
            <li><a href="{{ route('admin.reports.index', ['status' => 'all']) }}" @if ($status === 'all') aria-current="page" @endif>{{ __('admin.filter_all') }}</a></li>
        </ul>
    </nav>

    @if ($reports->isEmpty())
        <p>{{ __('admin.none') }}</p>
    @else
        <ul class="reports">
            @foreach ($reports as $report)
                <li class="card">
                    <h2><a href="{{ route('admin.reports.show', $report) }}">{{ $report->facility->name }}</a></h2>
                    <div class="badges">
                        @if ($report->status === 'new')
                            <span class="badge warn">{{ __('admin.status_new') }}</span>
                        @else
                            <span class="badge">{{ __('admin.status_resolved') }}</span>
                        @endif
                        @if ($report->contact)
                            <span class="badge">{{ __('admin.contact_given') }}</span>
                        @endif
                    </div>
                    <p>{{ __('admin.received', ['date' => $report->created_at->format('j F Y, H:i')]) }}</p>
                    <p>{{ \Illuminate\Support\Str::limit($report->message, 160) }}</p>
                </li>
            @endforeach
        </ul>

        @if ($reports->hasPages())
            <div class="pager">
                @if ($reports->previousPageUrl())
                    <a href="{{ $reports->previousPageUrl() }}" rel="prev">{{ __('admin.previous') }}</a>
                @else
                    <span></span>
                @endif
                @if ($reports->nextPageUrl())
                    <a href="{{ $reports->nextPageUrl() }}" rel="next">{{ __('admin.next') }}</a>
                @endif
            </div>
        @endif
    @endif
@endsection
