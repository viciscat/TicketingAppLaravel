<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticketing98 - @yield('title')</title>
    <link href="https://unpkg.com/98.css@0.1.21/dist/98.css" rel="stylesheet" type="text/css"/>
    <link href="{{asset("css/styles.css")}}" rel="stylesheet" type="text/css">
    <script src="{{ asset('js/index.js') }} " type="text/javascript" defer></script>
    <script src="{{asset('js/formHelper.js')}}" defer type="text/javascript"></script>
</head>
<body class="@yield('body_class')">
@yield("body")
</body>
</html>
