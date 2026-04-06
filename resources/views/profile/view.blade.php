@extends("layout.main")
@section("title")
    Profile Info
@endsection
@section("content")
    <b>Name</b>
    <p>{{ $user->fullName() }}</p>
    <b>Role</b>
    <p>{{ $user->role->getName() }}</p>
    <b>Email</b>
    <p><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
    <button onclick="location.href = '{{route("profile.edit")}}'">Edit Profile</button>
@endsection
