<section>
    <header>
        <div class="flex-row gap-4 margin-top-1">
            <img src="{{ asset("images/icons/recycle_bin_empty-0.png") }}" alt="Recycle bind">
            <span><b>Delete Account</b></span>
        </div>


        <p>
            Once your account is deleted, all of its resources and data will be permanently deleted. <br> Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <button onclick="document.getElementById('confirm-user-deletion').showModal()">
        Delete Account
    </button>

    <dialog id="confirm-user-deletion" {{ $errors->userDeletion->isNotEmpty() ? 'open' : '' }}>
        <div class="window">
            <div class="title-bar">
                <div class="title-bar-text">Delete Account Confirmation</div>
            </div>
            <div class="window-body">
                <form method="post" action="{{ route('profile.destroy') }}" class="basic-form">
                    @csrf
                    @method('delete')

                    <p>
                        <b>
                            Are you sure you want to delete your account?
                        </b>
                    </p>

                    <p>
                        Once your account is deleted, all of its resources and data will be permanently deleted. <br>
                        Please enter your password to confirm you would like to permanently delete your account.
                    </p>

                    <div class="field-row-stacked">
                        <label for="password">Password</label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Password"
                        />

                        <x-input-error for="password" bag="userDeletion"/>
                    </div>

                    <div class="margin-top-1">
                        <button type="button" onclick="document.getElementById('confirm-user-deletion').close()">
                            Cancel
                        </button>

                        <button type="submit">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </dialog>
</section>
