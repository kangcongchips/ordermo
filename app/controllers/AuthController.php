<?php

class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('auth/login', [
            'title' => 'Log in / Sign up - ordermo',
        ]);
    }

    public function register(): void
    {
        $this->view('auth/register', [
            'title' => 'Sign up - ordermo',
        ]);
    }
}
