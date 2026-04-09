@php use App\Enums\UserRole; @endphp
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
    @if( $self )
        <button onclick="location.href = '{{route("profile.edit")}}'">Edit Profile</button>
    @endif

    @if( $canEditRole )
        <p><b>Edit user role</b></p>
        <form class="basic-form" action="{{ route('user.role.update', $user->id) }}" method="post">
            @csrf
            @method('PATCH')
            <div class="field-row-stacked">
                <label for="role">Role</label>
                <x-select id="role" :default="$user->role" :options="UserRole::keyToName()"/>
                <x-input-error for="role"/>
            </div>
            <input type="submit" value="Save"/>
        </form>
    @endif
@endsection
