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

        $this->render('admin/products', 'products', 'Products', [
            'products' => $this->model('MenuItem')->allWithMerchant(),
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

        $this->render('admin/orders', 'orders', 'Orders', [
            'orders' => $this->model('Order')->allWithMeta(),
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

}
