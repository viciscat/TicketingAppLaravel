@extends("layout.empty")
@section("title")
    Register
@endsection
@section("body")
<div class="centered-container window">
    <section>
        <div class="title-bar">
            <div class="title-bar-text">Register to Ticketing98</div>
        </div>
        <!-- FIXME ACTION -->
        <!-- FIXME ERRORS THINGIES -->
        <form class="window-body" id="login-form" method="post">
            <!-- First Name -->
            <div class="field-row-stacked">
                <label for="first-name">First Name</label>
                <input id="first-name" name="first-name" placeholder="First Name" required type="text">
                <div class="hidden font-13px error-message" id="first-name-error">
                    <img alt="Error First Name" height="16" src="{{asset("images/icons/msg_error-2.png")}}" width="16">
                    <span></span>
                </div>
            </div>
            <!-- Last Name -->
            <div class="field-row-stacked">
                <label for="last-name">Last Name</label>
                <input id="last-name" name="last-name" placeholder="Last Name" required type="text">
                <div class="hidden font-13px error-message" id="last-name-error">
                    <img alt="Error Last Name" height="16" src="{{asset("images/icons/msg_error-2.png")}}" width="16">
                    <span></span>
                </div>
            </div>
            <!-- Email -->
            <div class="field-row-stacked">
                <label for="email">Email</label>
                <input id="email" name="email" placeholder="Email" required type="email">
                <div class="hidden font-13px error-message" id="email-error">
                    <img alt="Error Email" height="16" src="{{asset("images/icons/msg_error-2.png")}}" width="16">
                    <span></span>
                </div>
            </div>
            <!-- Password -->
            <div class="field-row-stacked">
                <label for="password">Password</label>
                <input id="password" name="password" placeholder="Password" required type="password">
                <div class="hidden font-13px error-message" id="password-error">
                    <img alt="Error Password" height="16" src="{{asset("images/icons/msg_error-2.png")}}" width="16">
                    <span></span>
                </div>
            </div>
            <!-- Confirm Password-->
            <div class="field-row-stacked">
                <label for="confirm-password">Confirm Password</label>
                <input id="confirm-password" name="confirm-password" placeholder="Confirm Password" required
                       type="password">
                <div class="hidden font-13px error-message" id="confirm-password-error">
                    <img alt="Error Confirm Password" height="16" src="{{asset("images/icons/msg_error-2.png")}}" width="16">
                    <span></span>
                </div>
            </div>
            <br/>
            <div style="text-align: right"><input type="submit" value="Register"></div>
        </form>
        <script>
            document.getElementById('login-form').addEventListener('submit', (e) => {
                let email = document.getElementById('email');
                let first_name = document.getElementById('first-name');
                let last_name = document.getElementById('last-name');
                let password = document.getElementById('password');
                let confirmPassword = document.getElementById('confirm-password');

                let valid = true;

                valid &= checkInput(email, 'email-error', [emptyCondition("Email is required!"), emailCondition("Not a valid email address!")]);
                valid &= checkInput(first_name, 'first-name-error', [emptyCondition("A first name is required!")]);
                valid &= checkInput(last_name, 'last-name-error', [emptyCondition("A last name is required!")]);
                valid &= checkInput(password, 'password-error', [emptyCondition("Password is required!")]);
                valid &= checkInput(confirmPassword, 'confirm-password-error', [emptyCondition("You must confirm your password!"), {
                    errorPredicate: (input) => input.value !== password.value,
                    message: "Password does not match!"
                }]);
                if (!valid) e.preventDefault();
                return valid;
            })
        </script>
    </section>
</div>
@endsection
