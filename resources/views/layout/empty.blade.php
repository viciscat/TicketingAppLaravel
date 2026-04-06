<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ticketing98 - @yield('title')</title>
    <link href="{{asset("css/styles.css")}}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/index.js') }} " type="text/javascript" defer></script>
    <script src="{{asset('js/formHelper.js')}}" defer type="text/javascript"></script>
    <script>
        function csrf(headers) {
            headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]')?.content
            return headers;
        }
    </script>
</head>
<body class="@yield('body_class')">
@yield("body")
@yield("inline-script")
</body>
</html>
