<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?: config('app.fallback_locale')) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="Add your project description here"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    {{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" rel="preconnect"--}}
    {{--            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"--}}
    {{--            crossorigin="anonymous"></script>--}}

    {{--    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"--}}
    {{--            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"--}}
    {{--            crossorigin="anonymous"></script>--}}
    {{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"--}}
    {{--            integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"--}}
    {{--            crossorigin="anonymous"></script>--}}

    @routes
    @viteReactRefresh
    @vite([
        'resources/css/vendor.css',
        'resources/css/app.css',
        'resources/js/index.tsx'
    ])
    @inertiaHead
</head>
<body>
@inertia('app')
</body>
</html>
