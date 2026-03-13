@extends("layout.empty")
@section("body")
    <div class="centered-container window">
        <section>
            <div class="title-bar">
                <div class="title-bar-text">Welcome to Ticketing98</div>
            </div>
            <form class="window-body" id="login-form" method="post">
                <div class="login-form-input">
                    <label for="username">Username</label>
                    <input id="username" name="username" placeholder="Username" type="email" required>
                </div>
                <div id="username-error" class="hidden font-13px error-message">
                    <img src="assets/icons/msg_error-2.png" alt="Error Username" width="16" height="16">
                    <span>Username must not be empty!</span>
                </div>
                <div class="login-form-input">
                    <label for="password">Password</label>
                    <input id="password" name="password" placeholder="Password" type="password">
                </div>
                <div id="password-error" class="hidden font-13px error-message">
                    <img src="assets/icons/msg_error-2.png" alt="Error Password" width="16" height="16">
                    <span>Password must not be empty!</span>
                </div>
                <a href="forgot_password.blade.php">Forgot Password?</a>
                <div style="text-align: right"><input type="submit" value="Login"></div>
            </form>
            <script>
                document.getElementById('login-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    let username = document.getElementById('username');
                    let password = document.getElementById('password');

                    let valid = true;

                    valid &= checkInput(username, 'username-error', [emptyCondition("Username is required!"), emailCondition("Not a valid email address!")]);
                    valid &= checkInput(password, 'password-error', [emptyCondition("Password is required!")]);
                    if (valid) location.href = 'dashboard.html'
                })
            </script>
        </section>
    </div>
@endsection
