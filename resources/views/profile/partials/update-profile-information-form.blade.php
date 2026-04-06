<section>
    <header>
        <div class="flex-row gap-4 margin-top-1">
            <img src="{{ asset("images/icons/user_computer-1.png") }}" alt="Profile">
            <span><b>Profile Information</b></span>
        </div>

        <p>
            Update your account's profile information and email address.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="basic-form">
        @csrf
        @method('patch')

        <div class="field-row-stacked">
            <label for="first-name">First Name</label>
            <input id="first-name" name="first-name" type="text" value="{{old('first-name', $user->first_name)}}"
                   required autofocus autocomplete="given-name"/>
            <x-input-error for="first-name"/>
        </div>
        <div class="field-row-stacked">
            <label for="last-name">Last Name</label>
            <input id="last-name" name="last-name" type="text" value="{{old('last-name', $user->last_name)}}" required
                   autofocus autocomplete="family-name"/>
            <x-input-error for="last-name"/>
        </div>

        <div class="flex-row gap-8 margin-top-1">
            <input type="submit" value="Save"/>
            @if (session('status') === 'profile-updated')
                <span>Saved.</span>
            @endif
        </div>

    </form>
</section>
