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
        $merchantId    = (int) $_SESSION['merchant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if ($action === 'toggle_open') {
                $open = (string) ($_POST['open'] ?? '') === '1';
                $merchantModel->setOpen($merchantId, $open);
                $this->flashRedirect('merchant/dashboard',
                    $open ? 'You are now accepting orders.' : 'You have paused new orders.');
            }

            if ($action === 'setup_profile') {
                $cityId  = (int) ($_POST['city_id'] ?? 0);
                $cuisine = trim((string) ($_POST['cuisine'] ?? ''));

                if ($cityId <= 0 || $cuisine === '') {
                    $this->flashRedirect('merchant/dashboard',
                        'Please choose a city and at least one cuisine.');
                }

                [$coverImage, $imageError] = $this->handleImageUpload($_FILES['cover_image'] ?? null);
                if ($imageError !== null) {
                    $this->flashRedirect('merchant/dashboard', $imageError);
                }

                $merchantModel->updateProfileBasics($merchantId, $cityId, $cuisine, $coverImage);
                $this->flashRedirect('merchant/dashboard',
                    'Profile completed — customers can now discover your restaurant.');
            }

            $this->flashRedirect('merchant/dashboard', 'Unknown action.');
        }

        $profile = $this->loadProfile();

        $this->render('merchant/dashboard', 'dashboard', 'Dashboard', [
            'profile' => $profile,
            'stats'   => $merchantModel->statsForMerchant($merchantId),
            'cities'  => $this->model('City')->all(),
        ]);
    }

    public function orders(): void
    {
        $this->requireMerchant();

        $orderModel = $this->model('Order');
        $riderModel = $this->model('Rider');
        $merchantId = (int) $_SESSION['merchant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action  = (string) ($_POST['action'] ?? 'assign_rider');
            $orderId = (int) ($_POST['order_id'] ?? 0);

            if ($action === 'assign_rider') {
                $riderId = (int) ($_POST['rider_id'] ?? 0);

                if ($orderId <= 0 || $riderId <= 0) {
                    $this->flashRedirect('merchant/orders',
                        'Please pick a rider before saving.');
                }
                if (!$orderModel->merchantOwns($orderId, $merchantId)) {
                    $this->flashRedirect('merchant/orders', 'Could not assign that order.');
                }
                if ($orderModel->assignRider($orderId, $riderId)) {
                    $this->flashRedirect('merchant/orders',
                        'Rider assigned to order #' . $orderId . '.');
                }
                $this->flashRedirect('merchant/orders', 'Could not assign that rider.');
            }

            $this->flashRedirect('merchant/orders', 'Unknown action.');
        }

        $this->render('merchant/orders', 'orders', 'Orders', [
            'profile' => $this->loadProfile(),
            'orders'  => $orderModel->forMerchant($merchantId),
            'riders'  => $riderModel->availableForAssignment(),
        ]);
    }

    public function products(): void
    {
        $this->requireMerchant();

        $menuModel  = $this->model('MenuItem');
        $merchantId = (int) $_SESSION['merchant_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim((string) ($_POST['name'] ?? ''));
            $price       = trim((string) ($_POST['price'] ?? ''));
            $category    = trim((string) ($_POST['category'] ?? '')) ?: 'Others';
            $description = trim((string) ($_POST['description'] ?? ''));
            $isAvailable = isset($_POST['is_available']);

            if ($name === '') {
                $this->flashRedirect('merchant/products', 'Product name is required.');
            }
            if (!is_numeric($price) || (float) $price <= 0) {
                $this->flashRedirect('merchant/products', 'Price must be a positive number.');
            }

            [$uploadedImage, $imageError] = $this->handleImageUpload($_FILES['image'] ?? null);
            if ($imageError !== null) {
                $this->flashRedirect('merchant/products', $imageError);
            }

            try {
                $menuModel->create([
                    'merchant_id'  => $merchantId,
                    'name'         => $name,
                    'description'  => $description,
                    'price'        => $price,
                    'category'     => $category,
                    'image'        => $uploadedImage ?? '',
                    'is_available' => $isAvailable ? '1' : '0',
                ]);
                $this->flashRedirect('merchant/products', '"' . $name . '" added to your menu.');
            } catch (Throwable $e) {
                error_log('[merchant/products] add_product failed: ' . $e->getMessage());
                $this->flashRedirect('merchant/products',
                    'Could not add product: ' . $e->getMessage());
            }
        }

        $baseCategories = [
            'Appetizers', 'Burgers', 'Chicken', 'Combo Meals', 'Desserts',
            'Drinks', 'Filipino', 'Pasta', 'Rice Meals', 'Seafood',
            'Sides', 'Snacks', 'Others',
        ];
        $categories = array_values(array_unique(array_merge(
            $baseCategories,
            $menuModel->distinctCategories()
        )));
        natcasesort($categories);

        $this->render('merchant/products', 'products', 'Products', [
            'profile'    => $this->loadProfile(),
            'products'   => $menuModel->allForMerchant($merchantId),
            'categories' => array_values($categories),
        ]);
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

    /** Logged-in merchant's profile, or log them out if their record is gone. */
    private function loadProfile(): array
    {
        $profile = $this->model('Merchant')
            ->profileByUserId((int) $_SESSION['merchant_user_id']);
        if (!$profile) {
            $this->logout();
        }
        return $profile;
    }

    /** Render a merchant-portal page through the sidebar layout. */
    private function render(string $view, string $active, string $pageTitle, array $data = []): void
    {
        $this->view($view, array_merge([
            'title'     => $pageTitle . ' - ordermo',
            'active'    => $active,
            'pageTitle' => $pageTitle,
        ], $data), 'merchant');
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

    /**
     * Mirror of AdminController::handleImageUpload — keeps merchant product
     * uploads in the same `images/food/uploads/` folder so existing rendering
     * works identically.
     *
     * @return array{0:?string,1:?string} [savedFilename|null, errorMessage|null]
     */
    private function handleImageUpload(?array $file): array
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [null, 'Image upload failed. Please try again.'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return [null, 'Image is too large (max 5 MB).'];
        }

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $info = @getimagesize($file['tmp_name']);
        $mime = $info['mime'] ?? '';
        if (!isset($allowed[$mime])) {
            return [null, 'Image must be a JPG, PNG, WEBP or GIF file.'];
        }

        $dir = __DIR__ . '/../../public/images/food/uploads/';
        if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
            return [null, 'Could not save the image. Please try again.'];
        }

        $filename = 'p' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return [null, 'Could not save the image. Please try again.'];
        }

        return ['uploads/' . $filename, null];
    }
}
