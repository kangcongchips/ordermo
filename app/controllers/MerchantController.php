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
                } elseif ($merchant['application_status'] === 'pending') {
                    $errors[] = 'Your application is still pending admin approval.';
                } elseif ($merchant['application_status'] === 'rejected') {
                    $errors[] = 'Your application was rejected. Please contact support.';
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
        $errors  = [];
        $success = false;
        $old     = [
            'first_name'       => '',
            'last_name'        => '',
            'business_name'    => '',
            'business_address' => '',
            'email'            => '',
            'phone'            => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'first_name'       => trim($_POST['first_name'] ?? ''),
                'last_name'        => trim($_POST['last_name'] ?? ''),
                'business_name'    => trim($_POST['business_name'] ?? ''),
                'business_address' => trim($_POST['business_address'] ?? ''),
                'email'            => trim($_POST['email'] ?? ''),
                'phone'            => trim($_POST['phone'] ?? ''),
            ];
            $password        = (string) ($_POST['password'] ?? '');
            $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

            $normalizedPhone = User::normalizePhone($old['phone']);

            if ($old['first_name'] === '') {
                $errors[] = 'First name is required.';
            }
            if ($old['last_name'] === '') {
                $errors[] = 'Last name is required.';
            }
            if ($old['business_name'] === '') {
                $errors[] = 'Business name is required.';
            }
            if ($old['business_address'] === '') {
                $errors[] = 'Business address is required.';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }
            if (!preg_match('/^09\d{9}$/', $normalizedPhone)) {
                $errors[] = 'Enter a valid PH mobile number (e.g. 09171234567).';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
            if ($password !== $passwordConfirm) {
                $errors[] = 'Passwords do not match.';
            }

            if (!$errors && $this->model('User')->findByEmail($old['email'])) {
                $errors[] = 'That email is already registered.';
            }

            if (!$errors) {
                try {
                    $this->model('Merchant')->create([
                        'first_name'         => $old['first_name'],
                        'last_name'          => $old['last_name'],
                        'email'              => $old['email'],
                        'phone'              => $normalizedPhone,
                        'password'           => $password,
                        'business_name'      => $old['business_name'],
                        'business_address'   => $old['business_address'],
                        'city_id'            => '',
                        'cuisine'            => '',
                        'cover_image'        => '',
                        'application_status' => 'pending',
                    ]);
                    $success = true;
                    $old = [
                        'first_name' => '', 'last_name' => '', 'business_name' => '',
                        'business_address' => '', 'email' => '', 'phone' => '',
                    ];
                } catch (Throwable $e) {
                    $errors[] = 'Could not submit application. Please try again.';
                }
            }
        }

        $this->view('merchant/apply', [
            'title'   => 'Apply as Merchant - ordermo',
            'errors'  => $errors,
            'old'     => $old,
            'success' => $success,
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
