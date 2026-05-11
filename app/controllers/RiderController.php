<?php

class RiderController extends Controller
{
    public function index(): void
    {
        header('Location: ' . BASE_URL . 'rider/login');
        exit;
    }

    public function login(): void
    {
        $this->view('rider/login', [
            'title' => 'Rider Login - ordermo',
        ]);
    }

    public function apply(): void
    {
        $this->view('rider/apply', [
            'title' => 'Apply as Rider - ordermo',
        ]);
    }
}
