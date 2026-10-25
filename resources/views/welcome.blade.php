<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('home.title') }}</title>
    <meta name="description" content="{{ __('home.tagline') }}">
    <style>
        :root {
            --bg: #f7faf8;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #4b5563;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --border: #d1e7df;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0b1f1c;
                --card: #11302b;
                --text: #e6f4f1;
                --muted: #a7c7c0;
                --brand: #34d399;
                --brand-dark: #6ee7b7;
                --border: #1f4a42;
            }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        main {
            width: 100%;
            max-width: 40rem;
            padding: 2rem 1.5rem;
            text-align: center;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
        }
        img { width: 7rem; height: auto; }
        h1 { margin: 1rem 0 .25rem; font-size: 2rem; }
        .tagline { margin: 0 0 1rem; font-weight: 600; color: var(--brand); }
        p { margin: .75rem 0; color: var(--muted); }
        .actions { display: flex; flex-wrap: wrap; gap: .75rem; justify-content: center; margin: 1.5rem 0 1rem; }
        a.button {
            padding: .7rem 1.2rem;
            border-radius: .5rem;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid var(--brand);
        }
        a.primary { background: var(--brand); color: #fff; }
        a.secondary { color: var(--brand-dark); }
        a.button:focus-visible { outline: 3px solid var(--brand-dark); outline-offset: 2px; }
        .disclaimer {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            font-size: .875rem;
        }
    </style>
</head>
<body>
    <main>
        <img src="{{ asset('images/logo.png') }}" alt="{{ __('home.title') }} logo">
        <h1>{{ __('home.title') }}</h1>
        <p class="tagline">{{ __('home.tagline') }}</p>
        <p>{{ __('home.mission') }}</p>
        <p><strong>{{ __('home.status') }}</strong></p>
        <div class="actions">
            <a class="button primary" href="https://github.com/AfyaLinkAfrica/afyalink-web">{{ __('home.contribute') }}</a>
            <a class="button secondary" href="https://github.com/AfyaLinkAfrica/afyalink-web/labels/good%20first%20issue">{{ __('home.issues') }}</a>
        </div>
        <p class="disclaimer">{{ __('home.disclaimer') }}</p>
    </main>
</body>
</html>
