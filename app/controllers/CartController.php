<?php

class CartController extends Controller
{
    /** Flat delivery fee charged per order unless the restaurant is free-delivery. */
    private const DELIVERY_FEE = 49.0;

    /** Add a menu item to the cart. Requires the user to be logged in. */
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
        }

        $itemId = (int) ($_POST['menu_item_id'] ?? 0);
        $back   = $this->safePath($_POST['redirect'] ?? '');

        if (!$itemId) {
            $this->redirect($back);
        }

        // Not logged in: remember the intent and send them to log in first.
        if (empty($_SESSION['user_id'])) {
            $_SESSION['pending_cart_add'] = $itemId;
            $_SESSION['intended_url']     = $back;
            $_SESSION['flash']            = 'Please log in to add items to your cart.';
            $this->redirect('auth/login');
        }

        $item = $this->model('MenuItem')->find($itemId);

        if (!$item) {
            $_SESSION['flash'] = 'That item is no longer available.';
            $this->redirect($back);
        }

        $_SESSION['cart'][$itemId] = ($_SESSION['cart'][$itemId] ?? 0) + 1;
        $_SESSION['flash']         = $item['name'] . ' added to your cart.';

        $this->redirect($back);
    }

    /** Show the cart contents. */
    public function index(): void
    {
        $items = [];
        $total = 0.0;

        foreach ($_SESSION['cart'] ?? [] as $itemId => $qty) {
            $item = $this->model('MenuItem')->find((int) $itemId);
            if (!$item) {
                continue;
            }
            $item['qty']      = (int) $qty;
            $item['subtotal'] = (float) $item['price'] * (int) $qty;
            $total           += $item['subtotal'];
            $items[]          = $item;
        }

        $this->view('cart/index', [
            'title' => 'Your cart - ordermo',
            'items' => $items,
            'total' => $total,
        ]);
    }

    /** Remove a single item from the cart. */
    public function remove(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemId = (int) ($_POST['menu_item_id'] ?? 0);
            unset($_SESSION['cart'][$itemId]);
        }
        $this->redirect('cart');
    }

    /**
     * Checkout: review delivery details, then place one order per restaurant.
     * Requires login.
     */
    public function checkout(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = 'cart/checkout';
            $_SESSION['flash']        = 'Please log in to check out.';
            $this->redirect('auth/login');
        }

        $groups = $this->buildGroups();
        if (!$groups) {
            $_SESSION['flash'] = 'Your cart is empty.';
            $this->redirect('cart');
        }

        $userId   = (int) $_SESSION['user_id'];
        $user     = $this->model('User')->findById($userId);
        $customer = $this->model('Customer')->findByUserId($userId);

        $old = [
            'delivery_address' => $customer['default_address'] ?? '',
            'contact_phone'    => $user['phone'] ?? '',
            'notes'            => '',
        ];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'delivery_address' => trim($_POST['delivery_address'] ?? ''),
                'contact_phone'    => trim($_POST['contact_phone'] ?? ''),
                'notes'            => trim($_POST['notes'] ?? ''),
            ];

            if ($old['delivery_address'] === '') {
                $errors[] = 'Delivery address is required.';
            }
            if ($old['contact_phone'] === '') {
                $errors[] = 'Contact number is required.';
            }

            if (!$errors) {
                try {
                    $orderIds = $this->model('Order')->createFromCart(
                        $userId,
                        $old + ['payment_method' => 'cod'],
                        $groups
                    );

                    unset($_SESSION['cart']);
                    $_SESSION['last_order_ids'] = $orderIds;
                    $_SESSION['flash']          = 'Order placed! Thank you.';
                    $this->redirect('cart/confirmation');
                } catch (Throwable $e) {
                    $errors[] = 'We could not place your order. Please try again.';
                }
            }
        }

        [$summary, $grandTotal] = $this->summarize($groups);

        $this->view('cart/checkout', [
            'title'      => 'Checkout - ordermo',
            'groups'     => $summary,
            'grandTotal' => $grandTotal,
            'old'        => $old,
            'errors'     => $errors,
        ]);
    }

    /** Order confirmation for the just-placed orders. */
    public function confirmation(): void
    {
        $ids = $_SESSION['last_order_ids'] ?? [];
        unset($_SESSION['last_order_ids']);

        if (!$ids) {
            $this->redirect('');
        }

        $orders     = [];
        $grandTotal = 0.0;
        foreach ($ids as $id) {
            $order = $this->model('Order')->findWithItems((int) $id);
            if ($order) {
                $orders[]    = $order;
                $grandTotal += (float) $order['total'];
            }
        }

        $this->view('cart/confirmation', [
            'title'      => 'Order confirmed - ordermo',
            'orders'     => $orders,
            'grandTotal' => $grandTotal,
        ]);
    }

    /**
     * Resolve the session cart into per-restaurant groups, dropping items that
     * are no longer available.
     *
     * @return array merchant_id => [business_name, delivery_fee, subtotal, items]
     */
    private function buildGroups(): array
    {
        $groups = [];

        foreach ($_SESSION['cart'] ?? [] as $itemId => $qty) {
            $item = $this->model('MenuItem')->find((int) $itemId);
            if (!$item) {
                continue;
            }

            $qty        = max(1, (int) $qty);
            $price      = (float) $item['price'];
            $lineTotal  = $price * $qty;
            $merchantId = (int) $item['merchant_id'];

            if (!isset($groups[$merchantId])) {
                $groups[$merchantId] = [
                    'business_name' => $item['business_name'],
                    'delivery_fee'  => (int) $item['free_delivery'] === 1 ? 0.0 : self::DELIVERY_FEE,
                    'subtotal'      => 0.0,
                    'items'         => [],
                ];
            }

            $groups[$merchantId]['items'][] = [
                'menu_item_id' => (int) $item['id'],
                'name'         => $item['name'],
                'price'        => $price,
                'qty'          => $qty,
                'subtotal'     => $lineTotal,
            ];
            $groups[$merchantId]['subtotal'] += $lineTotal;
        }

        return $groups;
    }

    /** Add per-group totals and compute the grand total for display. */
    private function summarize(array $groups): array
    {
        $grandTotal = 0.0;
        foreach ($groups as &$group) {
            $group['total'] = $group['subtotal'] + $group['delivery_fee'];
            $grandTotal    += $group['total'];
        }
        unset($group);

        return [$groups, $grandTotal];
    }

    /** Redirect to a path relative to BASE_URL and stop. */
    private function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . ltrim($path, '/'));
        exit;
    }

    /** Whitelist an internal relative path so it is safe to redirect to. */
    private function safePath(string $path): string
    {
        $path = trim($path);
        if ($path === '' || !preg_match('#^[A-Za-z0-9/_-]+$#', $path)) {
            return '';
        }
        return $path;
    }
}
