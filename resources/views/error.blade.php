@extends("layout.main")
@section("title")
    Error
@endsection

@section("content")
    <p>{{ $message }}</p>
    <button onclick="location.href = '{{ route($goBack) }}'">Go Back</button>
@endsection
