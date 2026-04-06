@extends("layout.main")
@section("title")
    Edit Profile
@endsection
@section("content")
    <div>
        @include('profile.partials.update-profile-information-form')
    </div>

    <div>
        @include('profile.partials.update-password-form')
    </div>

    <div>
        @include('profile.partials.delete-user-form')
    </div>
@endsection
