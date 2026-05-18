<?php

class AdminController extends Controller
{
    public function index(): void
    {
        $target = !empty($_SESSION['admin_id']) ? 'admin/dashboard' : 'admin/login';
        $this->redirect($target);
    }

    public function login(): void
    {
        if (!empty($_SESSION['admin_id'])) {
            $this->redirect('admin/dashboard');
        }

        $errors = [];
        $old    = ['username' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['username'] = trim($_POST['username'] ?? '');
            $password        = (string) ($_POST['password'] ?? '');

            if ($old['username'] === '' || $password === '') {
                $errors[] = 'Username and password are required.';
            }

            if (!$errors) {
                $adminModel = $this->model('Admin');
                $admin      = $adminModel->findByUsername($old['username']);

                if (!$admin || !password_verify($password, $admin['password_hash'])) {
                    $errors[] = 'Invalid username or password.';
                } elseif ((int) $admin['is_active'] !== 1) {
                    $errors[] = 'This admin account is disabled.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['admin_id']   = (int) $admin['id'];
                    $_SESSION['admin_name'] = $admin['full_name'];
                    $adminModel->touchLastLogin((int) $admin['id']);
                    $this->redirect('admin/dashboard');
                }
            }
        }

        $this->view('admin/login', [
            'title'  => 'Admin Login - ordermo',
            'errors' => $errors,
            'old'    => $old,
        ], 'minimal');
    }

    public function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_name']);
        $this->redirect('admin/login');
    }

    public function dashboard(): void
    {
        $this->requireAdmin();

        $this->render('admin/dashboard', 'dashboard', 'Dashboard', [
            'stats'  => $this->model('Admin')->dashboardStats(),
            'recent' => $this->model('Order')->allWithMeta(8),
        ]);
    }

    public function riders(): void
    {
        $this->requireAdmin();
        $riderModel = $this->model('Rider');

        $errors = [];
        $old    = $this->blankPerson() + ['vehicle_type' => 'motorcycle', 'license_number' => '', 'application_status' => 'approved'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old      = $this->personInput() + [
                'vehicle_type'       => $_POST['vehicle_type'] ?? 'motorcycle',
                'license_number'     => trim($_POST['license_number'] ?? ''),
                'application_status' => $_POST['application_status'] ?? 'approved',
            ];
            $password = (string) ($_POST['password'] ?? '');
            $errors   = $this->validatePerson($old, $password);

            if (!in_array($old['vehicle_type'], ['motorcycle', 'bicycle', 'car'], true)) {
                $errors[] = 'Choose a valid vehicle type.';
            }
            if ($old['license_number'] === '') {
                $errors[] = 'License number is required.';
            }
            if (!in_array($old['application_status'], ['pending', 'approved', 'rejected'], true)) {
                $errors[] = 'Choose a valid application status.';
            }

            if (!$errors) {
                try {
                    $riderModel->create($old + ['password' => $password]);
                    $this->flashRedirect('admin/riders', 'Rider "' . $old['first_name'] . ' ' . $old['last_name'] . '" created.');
                } catch (Throwable $e) {
                    $errors[] = 'Could not create rider. Please try again.';
                }
            }
        }

        $this->render('admin/riders', 'riders', 'Riders', [
            'riders' => $riderModel->allWithUser(),
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    public function merchants(): void
    {
        $this->requireAdmin();
        $merchantModel = $this->model('Merchant');

        $errors = [];
        $old    = $this->blankPerson() + [
            'business_name'      => '',
            'business_address'   => '',
            'city_id'            => '',
            'cuisine'            => '',
            'application_status' => 'approved',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = $this->personInput() + [
                'business_name'      => trim($_POST['business_name'] ?? ''),
                'business_address'   => trim($_POST['business_address'] ?? ''),
                'city_id'            => trim($_POST['city_id'] ?? ''),
                'cuisine'            => trim($_POST['cuisine'] ?? ''),
                'application_status' => $_POST['application_status'] ?? 'approved',
            ];
            $password = (string) ($_POST['password'] ?? '');
            $errors   = $this->validatePerson($old, $password);

            if ($old['business_name'] === '') {
                $errors[] = 'Business name is required.';
            }
            if ($old['business_address'] === '') {
                $errors[] = 'Business address is required.';
            }
            if (!in_array($old['application_status'], ['pending', 'approved', 'rejected'], true)) {
                $errors[] = 'Choose a valid application status.';
            }

            if (!$errors) {
                try {
                    $merchantModel->create($old + ['password' => $password]);
                    $this->flashRedirect('admin/merchants', 'Merchant "' . $old['business_name'] . '" created.');
                } catch (Throwable $e) {
                    $errors[] = 'Could not create merchant. Please try again.';
                }
            }
        }

        $this->render('admin/merchants', 'merchants', 'Merchants', [
            'merchants' => $merchantModel->allWithUser(),
            'cities'    => $this->model('City')->all(),
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    public function products(): void
    {
        $this->requireAdmin();
        $menuModel     = $this->model('MenuItem');
        $merchantModel = $this->model('Merchant');

        $errors = [];
        $old    = [
            'merchant_id' => '',
            'name'        => '',
            'description' => '',
            'price'       => '',
            'category'    => '',
            'image'       => '',
            'is_available' => '1',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'merchant_id'  => trim($_POST['merchant_id'] ?? ''),
                'name'         => trim($_POST['name'] ?? ''),
                'description'  => trim($_POST['description'] ?? ''),
                'price'        => trim($_POST['price'] ?? ''),
                'category'     => trim($_POST['category'] ?? ''),
                'image'        => trim($_POST['image'] ?? ''),
                'is_available' => isset($_POST['is_available']) ? '1' : '0',
            ];

            if ($old['merchant_id'] === '' || !ctype_digit($old['merchant_id'])) {
                $errors[] = 'Select a merchant.';
            }
            if ($old['name'] === '') {
                $errors[] = 'Product name is required.';
            }
            if (!is_numeric($old['price']) || (float) $old['price'] <= 0) {
                $errors[] = 'Price must be a positive number.';
            }
            if ($old['category'] === '') {
                $old['category'] = 'Others';
            }

            if (!$errors) {
                try {
                    $menuModel->create($old);
                    $this->flashRedirect('admin/products', 'Product "' . $old['name'] . '" added.');
                } catch (Throwable $e) {
                    $errors[] = 'Could not add product. Please try again.';
                }
            }
        }

        $this->render('admin/products', 'products', 'Products', [
            'products'  => $menuModel->allWithMerchant(),
            'merchants' => $merchantModel->listForSelect(),
            'errors'    => $errors,
            'old'       => $old,
        ]);
    }

    public function customers(): void
    {
        $this->requireAdmin();

        $this->render('admin/customers', 'customers', 'Customers', [
            'customers' => $this->model('Customer')->allWithUser(),
        ]);
    }

    public function orders(): void
    {
        $this->requireAdmin();
        $orderModel = $this->model('Order');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int) ($_POST['order_id'] ?? 0);
            $status  = (string) ($_POST['status'] ?? '');

            if ($orderId > 0 && $orderModel->updateStatus($orderId, $status)) {
                $this->flashRedirect('admin/orders', 'Order #' . $orderId . ' set to ' . str_replace('_', ' ', $status) . '.');
            }
            $this->flashRedirect('admin/orders', 'Could not update that order.');
        }

        $this->render('admin/orders', 'orders', 'Orders', [
            'orders'   => $orderModel->allWithMeta(),
            'statuses' => Order::STATUSES,
        ]);
    }

    // --- helpers -----------------------------------------------------------

    private function requireAdmin(): void
    {
        if (empty($_SESSION['admin_id'])) {
            $this->redirect('admin/login');
        }
    }

    private function render(string $view, string $active, string $pageTitle, array $data = []): void
    {
        $this->view($view, array_merge([
            'title'     => $pageTitle . ' - ordermo Admin',
            'active'    => $active,
            'pageTitle' => $pageTitle,
            'errors'    => $data['errors'] ?? [],
        ], $data), 'admin');
    }

    private function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    private function flashRedirect(string $path, string $message): void
    {
        $_SESSION['admin_flash'] = $message;
        $this->redirect($path);
    }

    /** @return array{first_name:string,last_name:string,email:string,phone:string} */
    private function blankPerson(): array
    {
        return ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => ''];
    }

    private function personInput(): array
    {
        return [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name'  => trim($_POST['last_name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'phone'      => User::normalizePhone($_POST['phone'] ?? ''),
        ];
    }

    /** Shared validation for the user account behind a rider/merchant. */
    private function validatePerson(array &$d, string $password): array
    {
        $errors = [];

        if ($d['first_name'] === '') {
            $errors[] = 'First name is required.';
        }
        if ($d['last_name'] === '') {
            $errors[] = 'Last name is required.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }
        if (!preg_match('/^09\d{9}$/', $d['phone'])) {
            $errors[] = 'Enter a valid PH mobile number (e.g. 09171234567).';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if (!$errors && $this->model('User')->findByEmail($d['email'])) {
            $errors[] = 'That email is already registered.';
        }

        return $errors;
    }
}
