<section>
    <header>
        <div class="flex-row gap-4 margin-top-1">
            <img src="{{ asset("images/icons/keys-2.png") }}" alt="Keys">
            <span><b>Update Password</b></span>
        </div>

        <p>
            Ensure your account is using a long, random password to stay secure
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="basic-form">
        @csrf
        @method('put')

        <div class="field-row-stacked">
            <label for="current_password">Current Password</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password"/>
            <x-input-error for="current_password"/>
        </div>

        <div class="field-row-stacked">
            <label for="password">Update password</label>
            <input id="password" name="password" type="password" autocomplete="new-password"/>
            <x-input-error for="password"/>
        </div>

        <div class="field-row-stacked">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"/>
            <x-input-error for="password_confirmation"/>
        </div>

        <div class="flex-row gap-8 margin-top-1">
            <input type="submit" value="Save" />
            @if (session('status') === 'password-updated')
                <p>Saved.</p>
            @endif
        </div>
    </form>
</section>
