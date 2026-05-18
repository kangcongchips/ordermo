<?php

class RestaurantController extends Controller
{
    public function index($id = null): void
    {
        $merchantId = (int) $id;
        $restaurant = $this->model('Merchant')->find($merchantId);

        if (!$restaurant) {
            http_response_code(404);
            $this->view('errors/not-found', [
                'title'   => 'Restaurant not found - ordermo',
                'message' => 'We could not find that restaurant.',
            ]);
            return;
        }

        $menu = $this->model('MenuItem')->getByMerchantGrouped($merchantId);

        $this->view('restaurant/index', [
            'title'      => $restaurant['business_name'] . ' - ordermo',
            'restaurant' => $restaurant,
            'menu'       => $menu,
        ]);
    }
}
