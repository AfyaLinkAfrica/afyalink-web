@extends('layouts.app')

@section('title', __('admin.login_title'))

@push('styles')
<style>
    input[type="email"], input[type="password"] {
        width: 100%;
        min-height: 2.75rem;
        padding: .5rem .75rem;
        font: inherit;
        color: var(--text);
        background: var(--bg);
        border: 2px solid var(--border);
        border-radius: .5rem;
    }
    input[type="email"]:focus-visible, input[type="password"]:focus-visible {
        outline: 3px solid var(--brand-dark);
        outline-offset: 2px;
    }
</style>
@endpush

@section('content')
    <section class="card">
        <h1>{{ __('admin.login_title') }}</h1>
        <p>{{ __('admin.login_intro') }}</p>

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div class="field">
                <label for="email">{{ __('admin.email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                @error('email')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="password">{{ __('admin.password') }}</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button">{{ __('admin.sign_in') }}</button>
        </form>
    </section>
@endsection
