<?php

class MerchantController extends Controller
{
    public function index(): void
    {
        header('Location: ' . BASE_URL . 'merchant/login');
        exit;
    }

    public function login(): void
    {
        $this->view('merchant/login', [
            'title' => 'Merchant Login - ordermo',
        ]);
    }

    public function apply(): void
    {
        $this->view('merchant/apply', [
            'title' => 'Apply as Merchant - ordermo',
        ]);
    }
}
