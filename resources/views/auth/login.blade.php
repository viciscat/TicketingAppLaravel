@extends("layout.empty")
@section("body")
    <div class="centered-container window">
        <section>
            <div class="title-bar">
                <div class="title-bar-text">Welcome to Ticketing98</div>
            </div>
            <form class="window-body" id="login-form" method="post" action="{{route("login")}}">
                @csrf
                <div class="login-form-input">
                    <label for="email">Email</label>
                    <input id="email" name="email" placeholder="Username" type="email" required value="{{old("email")}}">
                </div>
                <x-input-error alt="Username" for="email"/>
                <div class="login-form-input">
                    <label for="password">Password</label>
                    <input id="password" name="password" placeholder="Password" type="password">
                </div>
                <x-input-error alt="Password" for="password"/>
                <a href="{{route("password.request")}}">Forgot Password?</a>
                <div style="text-align: right"><input type="submit" value="Login"></div>
            </form>
            <script>
                document.getElementById('login-form').addEventListener('submit', (e) => {
                    let username = document.getElementById('email');
                    let password = document.getElementById('password');

                    let valid = true;

                    valid &= checkInput(username, 'email-error', [emptyCondition("Email is required!"), emailCondition("Not a valid email address!")]);
                    valid &= checkInput(password, 'password-error', [emptyCondition("Password is required!")]);
                    if (!valid) e.preventDefault();
                    return valid;
                })
            </script>
        </section>
    </div>
@endsection
