<?php

class HomeController extends Controller
{
    public function index(): void
    {
        $city  = $this->model('City');
        $cities = $city->getFeatured();

        $this->view('home/index', [
            'title'  => 'ordermo — Get what you need',
            'cities' => $cities,
        ]);
    }
}
