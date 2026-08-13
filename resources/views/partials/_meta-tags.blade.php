
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Primary Meta Tags -->
<meta name="title" content="@yield('metatitle', config('app.name'))">
<meta name="description" content="@yield('metadescription', \App\Models\SiteSetting::first()->site_description ?? '')">
<meta name="keywords" content="@yield('metakeywords', \App\Models\SiteSetting::first()->site_keywords ?? '')">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('metatitle', config('app.name'))">
<meta property="og:description" content="@yield('metadescription', \App\Models\SiteSetting::first()->site_description ?? '')">
<meta property="og:image" content="@yield('metaimage', \App\Models\SiteSetting::first()->site_logo ?? '')">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url()->current() }}">
<meta property="twitter:title" content="@yield('metatitle', config('app.name'))">
<meta property="twitter:description" content="@yield('metadescription', \App\Models\SiteSetting::first()->site_description ?? '')">
<meta property="twitter:image" content="@yield('metaimage', \App\Models\SiteSetting::first()->site_logo ?? '')">

<!-- Additional Meta Tags -->
<meta name="author" content="{{ config('app.name') }}">
<meta name="robots" content="@yield('metarobots', 'index, follow')">
<meta name="language" content="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta name="revisit-after" content="7 days">

<!-- PWA Meta Tags -->
<meta name="theme-color" content="#ffffff">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Favicon -->
<link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::first()->favicon ?? asset('favicon.ico') }}">