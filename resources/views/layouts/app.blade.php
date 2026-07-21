<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Tank Level SCADA Simulator' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="scadaDashboard" x-init="initCharts(); tick(); setInterval(() => tick(), 1000);" class="min-h-screen">
    <div class="mx-auto max-w-[1800px] px-4 py-4 lg:px-6">
        @yield('content')
    </div>
</body>
</html>
