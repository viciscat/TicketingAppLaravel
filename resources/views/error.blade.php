@extends("layout.main")
@section("title")
    Error
@endsection

@section("content")
    <p>{{ $message }}</p>
    <button onclick="location.href = '{{ $goBack }}'">Go Back</button>
@endsection
