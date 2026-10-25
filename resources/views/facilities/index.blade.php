@extends('layouts.app')

@section('title', __('facilities.heading'))

@section('content')
    <section class="card">
        <h1>{{ __('facilities.heading') }}</h1>
        <p>{{ __('facilities.coming_soon') }}</p>
        <div class="actions">
            <a class="button secondary" href="https://github.com/AfyaLinkAfrica/afyalink-web/issues">{{ __('facilities.view_issues') }}</a>
        </div>
    </section>
@endsection
