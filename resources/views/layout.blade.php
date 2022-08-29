<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>Vodafone Keycloak FrontApp</title>
    @include('snippets.favicons')

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/arsha.css') }}">
    <script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.js') }}"></script>
    @yield('head', '')
</head>
<body>
<div id="header" class="bg-secondary text-white" @yield('headerBgImageStyle', '')>
    <div class="ms-5"
        @yield('header', '')
    </div>
</div>
<div id="content">
    @yield('content', '')
</div>
<div id="footer">
    @yield('footer', '')
</div>

<script type="text/javascript">
</script>
</body>
</html>
