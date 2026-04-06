@extends("layout.main")
@section("title")
    @yield("error")
@endsection

@section("content")
    <img src="https://http.cat/@yield('code')" alt="Error @yield('code')"/>
@endsection
