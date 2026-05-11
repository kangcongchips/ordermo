<?php

class AdminController extends Controller
{
    public function index(): void
    {
        header('Location: ' . BASE_URL . 'admin/login');
        exit;
    }

    public function login(): void
    {
        $this->view('admin/login', [
            'title' => 'Admin Login - ordermo',
        ], 'minimal');
    }
}
