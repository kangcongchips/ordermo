<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $city     = $this->model('City');
        $provinces = $city->getGroupedByProvince();

        $this->view('home/index', [
            'title'     => 'ordermo.ph - We deliver your needs fast!',
            'provinces' => $provinces,
        ]);
    }
}
