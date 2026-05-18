<?php

class MerchantController extends Controller
{
    public function index(): void
    {
        $target = !empty($_SESSION['merchant_user_id']) ? 'merchant/dashboard' : 'merchant/login';
        $this->redirect($target);
    }

    public function login(): void
    {
        if (!empty($_SESSION['merchant_user_id'])) {
            $this->redirect('merchant/dashboard');
        }

        $errors = [];
        $old    = ['email' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['email'] = trim($_POST['email'] ?? '');
            $password     = (string) ($_POST['password'] ?? '');

            if ($old['email'] === '' || $password === '') {
                $errors[] = 'Email and password are required.';
            }

            if (!$errors) {
                $merchant = $this->model('Merchant')->findForLogin($old['email']);

                if (!$merchant || !password_verify($password, $merchant['password_hash'])) {
                    $errors[] = 'Invalid email or password.';
                } elseif ($merchant['status'] !== 'active') {
                    $errors[] = 'This account is not active. Please contact support.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['merchant_user_id'] = (int) $merchant['user_id'];
                    $_SESSION['merchant_id']      = (int) $merchant['merchant_id'];
                    $_SESSION['merchant_name']    = $merchant['business_name'];
                    $this->redirect('merchant/dashboard');
                }
            }
        }

        $this->view('merchant/login', [
            'title'  => 'Merchant Login - ordermo',
            'errors' => $errors,
            'old'    => $old,
        ], 'minimal');
    }

    public function logout(): void
    {
        unset(
            $_SESSION['merchant_user_id'],
            $_SESSION['merchant_id'],
            $_SESSION['merchant_name']
        );
        $this->redirect('merchant/login');
    }

    public function dashboard(): void
    {
        $this->requireMerchant();

        $merchantModel = $this->model('Merchant');
        $orderModel    = $this->model('Order');
        $merchantId    = (int) $_SESSION['merchant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if ($action === 'toggle_open') {
                $open = (string) ($_POST['open'] ?? '') === '1';
                $merchantModel->setOpen($merchantId, $open);
                $this->flashRedirect('merchant/dashboard',
                    $open ? 'Your restaurant is now open.' : 'Your restaurant is now closed.');
            }

            if ($action === 'order_status') {
                $orderId = (int) ($_POST['order_id'] ?? 0);
                $status  = (string) ($_POST['status'] ?? '');

                if ($orderId > 0 && $orderModel->merchantOwns($orderId, $merchantId)
                    && $orderModel->updateStatus($orderId, $status)) {
                    $this->flashRedirect('merchant/dashboard',
                        'Order #' . $orderId . ' set to ' . str_replace('_', ' ', $status) . '.');
                }
                $this->flashRedirect('merchant/dashboard', 'Could not update that order.');
            }

            $this->flashRedirect('merchant/dashboard', 'Unknown action.');
        }

        $profile = $merchantModel->profileByUserId((int) $_SESSION['merchant_user_id']);
        if (!$profile) {
            $this->logout();
        }

        $this->view('merchant/dashboard', [
            'title'        => 'Restaurant Dashboard - ordermo',
            'pageTitle'    => 'Dashboard',
            'portalRole'   => 'Restaurant',
            'portalName'   => $profile['business_name'],
            'portalHome'   => 'merchant/dashboard',
            'portalLogout' => 'merchant/logout',
            'profile'      => $profile,
            'stats'        => $merchantModel->statsForMerchant($merchantId),
            'orders'       => $orderModel->forMerchant($merchantId),
            'statuses'     => Order::STATUSES,
        ], 'portal');
    }

    public function apply(): void
    {
        $this->view('merchant/apply', [
            'title' => 'Apply as Merchant - ordermo',
        ]);
    }

    // --- helpers -----------------------------------------------------------

    private function requireMerchant(): void
    {
        if (empty($_SESSION['merchant_user_id'])) {
            $this->redirect('merchant/login');
        }
    }

    private function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    private function flashRedirect(string $path, string $message): void
    {
        $_SESSION['portal_flash'] = $message;
        $this->redirect($path);
    }
}
