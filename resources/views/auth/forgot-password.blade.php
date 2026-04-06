@extends("layout.empty")
@section("title")
    Forgot Password
@endsection
@section("body")
    <div class="centered-container window">
        <section>
            <div class="title-bar">
                <div class="title-bar-text">Forgot Password?</div>
            </div>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                Enter your email to receive a reset password link. <br/><br/>
                <label for="email">Email</label>
                <input id="email" name="email" placeholder="Email" type="email" required>
                <x-input-error for="email"/>
                <div style="text-align: right"><input type="submit" value="Reset Password"></div>
            </form>
            <script>
                document.getElementById('forgot-password-form').addEventListener('submit', (e) => {
                    let email = document.getElementById('email');

                    let valid = checkInput(email, 'email-error', [emptyCondition("Please specify an email!"), emailCondition("Invalid email address!")]);

                    if(!valid) e.preventDefault();
                    return valid;
                })
            </script>
        </section>
    </div>
@endsection
