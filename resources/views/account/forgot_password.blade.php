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
        <form class="window-body" id="forgot-password-form" method="post">
            Enter your email to receive a reset password link. <br/><br/>
            <label for="email">Email</label>
            <input id="email" name="email" placeholder="Email" type="email" required>
            <div id="email-error" class="hidden font-13px error-message">
                <img src="{{asset("images/icons/msg_error-2.png")}}" alt="Error Username" width="16" height="16">
                <span>Username must not be empty!</span>
            </div>
            <div id="success-message" class="hidden">
                The email has been sent! You will be redirected to the login page shortly.
            </div>
            <div style="text-align: right"><input type="submit" value="Reset Password"></div>
        </form>
        <script>
            document.getElementById('forgot-password-form').addEventListener('submit', (e) => {
                e.preventDefault();
                let email = document.getElementById('email');

                let valid = checkInput(email, 'email-error', [emptyCondition("Please specify an email!"), emailCondition("Invalid email address!")]);

                if (valid) {
                    let successMessage = document.getElementById('success-message');
                    successMessage.classList.remove('hidden');
                    setTimeout(() => location.href = "login.html", 3000);
                }
            })
        </script>
    </section>
</div>
@endsection
