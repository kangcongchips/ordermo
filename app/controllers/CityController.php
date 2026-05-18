<?php

class CityController extends Controller
{
    public function index($id = null): void
    {
        $cityId = (int) $id;
        $city   = $this->model('City')->find($cityId);

        if (!$city) {
            http_response_code(404);
            $this->view('errors/not-found', [
                'title'   => 'City not found - ordermo',
                'message' => 'We could not find that city.',
            ]);
            return;
        }

        $restaurants = $this->model('Merchant')->getByCity($cityId);

        $this->view('city/index', [
            'title'       => $city['name'] . ' - ordermo',
            'city'        => $city,
            'restaurants' => $restaurants,
        ]);
    }
}
