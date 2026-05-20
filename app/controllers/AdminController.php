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
            'stats'           => $this->model('Admin')->dashboardStats(),
            'recent'          => $this->model('Order')->allWithMeta(8),
            'pendingRiders'   => $this->model('Rider')->pendingApplications(),
            'pendingMerchants'=> $this->model('Merchant')->pendingApplications(),
        ]);
    }

    public function riders(): void
    {
        $this->requireAdmin();
        $riderModel = $this->model('Rider');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $riderId = (int) ($_POST['rider_id'] ?? 0);
            $status  = (string) ($_POST['application_status'] ?? '');

            if ($riderId > 0 && $riderModel->updateApplicationStatus($riderId, $status)) {
                $label = $status === 'approved' ? 'approved'
                    : ($status === 'rejected' ? 'rejected' : 'set to pending');
                $this->flashRedirect('admin/riders', 'Rider #' . $riderId . ' ' . $label . '.');
            }
            $this->flashRedirect('admin/riders', 'Could not update that rider.');
        }

        $this->render('admin/riders', 'riders', 'Riders', [
            'riders'  => $riderModel->allWithUser(),
            'pending' => $riderModel->pendingApplications(),
        ]);
    }

    public function merchants(): void
    {
        $this->requireAdmin();
        $merchantModel = $this->model('Merchant');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $merchantId = (int) ($_POST['merchant_id'] ?? 0);
            $status     = (string) ($_POST['application_status'] ?? '');

            if ($merchantId > 0 && $merchantModel->updateApplicationStatus($merchantId, $status)) {
                $label = $status === 'approved' ? 'approved'
                    : ($status === 'rejected' ? 'rejected' : 'set to pending');
                $this->flashRedirect('admin/merchants', 'Restaurant #' . $merchantId . ' ' . $label . '.');
            }
            $this->flashRedirect('admin/merchants', 'Could not update that restaurant.');
        }

        $this->render('admin/merchants', 'merchants', 'Restaurants', [
            'merchants' => $merchantModel->allWithUser(),
            'pending'   => $merchantModel->pendingApplications(),
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
                'image'        => '',
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

            [$uploadedImage, $imageError] = $this->handleImageUpload($_FILES['image'] ?? null);
            if ($imageError !== null) {
                $errors[] = $imageError;
            }

            if (!$errors) {
                $old['image'] = $uploadedImage ?? '';
                try {
                    $menuModel->create($old);
                    $this->flashRedirect('admin/products', 'Product "' . $old['name'] . '" added.');
                } catch (Throwable $e) {
                    $errors[] = 'Could not add product. Please try again.';
                }
            }
        }

        $baseCategories = [
            'Appetizers', 'Burgers', 'Chicken', 'Combo Meals', 'Desserts',
            'Drinks', 'Filipino', 'Pasta', 'Rice Meals', 'Seafood',
            'Sides', 'Snacks', 'Others',
        ];
        $categories = array_merge($baseCategories, $menuModel->distinctCategories());
        $categories = array_values(array_unique($categories));
        natcasesort($categories);

        $this->render('admin/products', 'products', 'Products', [
            'products'   => $menuModel->allWithMerchant(),
            'merchants'  => $merchantModel->listForSelect(),
            'categories' => array_values($categories),
            'errors'     => $errors,
            'old'        => $old,
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

    /**
     * Validate and store an uploaded product image under public/images/food/.
     *
     * @param  array|null $file A single entry from $_FILES.
     * @return array{0:?string,1:?string} [savedFilename|null, errorMessage|null].
     *         Both null means "no file was uploaded" (image is optional).
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

        // Dedicated, git-ignored folder for admin uploads. Stored as a path
        // relative to images/food/ so existing rendering keeps working and
        // seed images (e.g. "burger.jpg") are untouched.
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
