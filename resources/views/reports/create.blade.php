@extends('layouts.app')

@section('title', __('reports.heading'))

@section('content')
    <section class="card">
        <h1>{{ __('reports.heading') }}</h1>
        <p>{{ __('reports.about', ['facility' => $facility->name]) }}</p>
        <p class="notice" role="note">{{ __('reports.privacy') }}</p>

        <form method="post" action="{{ route('facilities.report.store', $facility) }}">
            @csrf

            <div class="field">
                <label for="message">{{ __('reports.message_label') }}</label>
                <textarea id="message" name="message" rows="5" minlength="10" maxlength="1000" required aria-describedby="message-help @error('message') message-error @enderror" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                <p class="help" id="message-help">{{ __('reports.message_help') }}</p>
                @error('message')
                    <p class="error" id="message-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="contact">{{ __('reports.contact_label') }}</label>
                <input id="contact" name="contact" type="text" maxlength="100" value="{{ old('contact') }}" aria-describedby="contact-help @error('contact') contact-error @enderror" @error('contact') aria-invalid="true" @enderror>
                <p class="help" id="contact-help">{{ __('reports.contact_help') }}</p>
                @error('contact')
                    <p class="error" id="contact-error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="honeypot" aria-hidden="true">
                <label for="hp_check">{{ __('reports.honeypot_label') }}</label>
                <input id="hp_check" name="hp_check" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="actions">
                <button type="submit" class="button">{{ __('reports.submit') }}</button>
                <a class="button secondary" href="{{ route('facilities.show', $facility) }}">{{ __('reports.cancel') }}</a>
            </div>
        </form>
    </section>
@endsection
