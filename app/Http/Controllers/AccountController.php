<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function login() {
        return view('account.login');
    }

    public function forgotPassword() {
        return view('account.forgot_password');
    }

    public function register() {
        return view('account.register');
    }

    public function viewProfile() {
        return view('account.profile');
    }
}
