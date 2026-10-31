<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') | @endif{{ __('layout.site_name') }}</title>
    <meta name="description" content="{{ __('home.tagline') }}">
    <style>
        :root {
            --bg: #f7faf8;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #4b5563;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --on-brand: #ffffff;
            --border: #d1e7df;
            --alert-bg: #fff7ed;
            --alert-text: #7c2d12;
            --alert-border: #fdba74;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0b1f1c;
                --card: #11302b;
                --text: #e6f4f1;
                --muted: #a7c7c0;
                --brand: #34d399;
                --brand-dark: #6ee7b7;
                --on-brand: #04201c;
                --border: #1f4a42;
                --alert-bg: #3b1d0e;
                --alert-text: #fed7aa;
                --alert-border: #9a3412;
            }
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        a { color: var(--brand-dark); }
        a:focus-visible, .skip-link:focus-visible { outline: 3px solid var(--brand-dark); outline-offset: 2px; }
        .skip-link {
            position: absolute;
            left: -999px;
            top: 0;
            padding: .5rem 1rem;
            background: var(--brand);
            color: var(--on-brand);
            z-index: 10;
        }
        .skip-link:focus { left: 0; }
        .emergency {
            padding: .6rem 1rem;
            text-align: center;
            font-size: .9rem;
            font-weight: 600;
            background: var(--alert-bg);
            color: var(--alert-text);
            border-bottom: 1px solid var(--alert-border);
        }
        header.site-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .75rem 1.5rem;
            background: var(--card);
            border-bottom: 1px solid var(--border);
        }
        .brand { font-weight: 700; font-size: 1.15rem; text-decoration: none; color: var(--brand); }
        nav ul { display: flex; gap: 1rem; margin: 0; padding: 0; list-style: none; }
        nav a { text-decoration: none; font-weight: 600; padding: .25rem .1rem; border-bottom: 2px solid transparent; }
        nav a[aria-current="page"] { border-bottom-color: var(--brand); }
        main { flex: 1; width: 100%; max-width: 48rem; margin: 0 auto; padding: 2rem 1.5rem; }
        .card {
            padding: 2rem 1.5rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
        }
        .center { text-align: center; }
        h1 { margin: 0 0 .5rem; font-size: 2rem; line-height: 1.2; }
        p { margin: .75rem 0; color: var(--muted); }
        .tagline { font-weight: 600; color: var(--brand); }
        .actions { display: flex; flex-wrap: wrap; gap: .75rem; margin: 1.5rem 0 0; }
        .center .actions { justify-content: center; }
        a.button {
            padding: .7rem 1.2rem;
            border-radius: .5rem;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid var(--brand);
        }
        a.primary { background: var(--brand); color: var(--on-brand); }
        a.secondary { color: var(--brand-dark); }
        .badges { display: flex; flex-wrap: wrap; gap: .5rem; margin: .5rem 0; }
        .badge {
            display: inline-block;
            padding: .15rem .6rem;
            font-size: .8rem;
            font-weight: 700;
            border: 2px solid var(--border);
            border-radius: 999px;
            color: var(--brand-dark);
        }
        .badge.warn { color: var(--alert-text); background: var(--alert-bg); border-color: var(--alert-border); }
        .field { margin-bottom: 1rem; }
        label { display: block; margin-bottom: .25rem; font-weight: 600; }
        select, input[type="text"], textarea {
            width: 100%;
            min-height: 2.75rem;
            padding: .5rem .75rem;
            font: inherit;
            color: var(--text);
            background: var(--bg);
            border: 2px solid var(--border);
            border-radius: .5rem;
        }
        textarea { resize: vertical; }
        select:focus-visible, input[type="text"]:focus-visible, textarea:focus-visible, button:focus-visible {
            outline: 3px solid var(--brand-dark);
            outline-offset: 2px;
        }
        button.button {
            min-height: 2.75rem;
            padding: .7rem 1.2rem;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            color: var(--on-brand);
            background: var(--brand);
            border: 2px solid var(--brand);
            border-radius: .5rem;
        }
        .help { margin: .25rem 0 0; font-size: .875rem; }
        .error { margin: .35rem 0 0; font-size: .9rem; font-weight: 600; color: var(--alert-text); }
        .notice.success { color: var(--brand-dark); background: var(--card); border-color: var(--brand); }
        .honeypot { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
        ul.services { display: flex; flex-wrap: wrap; gap: .4rem; margin: .5rem 0 0; padding: 0; list-style: none; }
        ul.services li { padding: .1rem .6rem; font-size: .85rem; color: var(--muted); border: 1px solid var(--border); border-radius: 999px; }
        .notice {
            margin: 0 0 1rem;
            padding: .75rem 1rem;
            font-size: .95rem;
            font-weight: 600;
            color: var(--alert-text);
            background: var(--alert-bg);
            border: 1px solid var(--alert-border);
            border-radius: .5rem;
        }
        footer.site-footer {
            padding: 1.25rem 1.5rem;
            font-size: .875rem;
            text-align: center;
            background: var(--card);
            border-top: 1px solid var(--border);
        }
        footer.site-footer p { margin: .35rem 0; }
    </style>
    @stack('styles')
    @include('partials.pwa-head')
</head>
<body>
    <a class="skip-link" href="#main-content">{{ __('layout.skip_to_content') }}</a>

    <div class="emergency" role="note">{{ __('layout.emergency') }}</div>

    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">{{ __('layout.site_name') }}</a>
        <nav aria-label="{{ __('layout.nav_label') }}">
            <ul>
                <li><a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>{{ __('layout.nav_home') }}</a></li>
                <li><a href="{{ route('facilities.index') }}" @if (request()->routeIs('facilities.*')) aria-current="page" @endif>{{ __('layout.nav_facilities') }}</a></li>
            </ul>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <p>{{ __('layout.disclaimer') }}</p>
        <p>
            {{ __('layout.footer_open_source') }}
            <a href="https://github.com/AfyaLinkAfrica/afyalink-web">{{ __('layout.footer_github') }}</a>
        </p>
    </footer>
    @include('partials.pwa-script')
</body>
</html>
