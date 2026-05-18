<?php

class RiderController extends Controller
{
    /** Status changes a rider is allowed to make on the delivery board. */
    private const RIDER_STATUSES = ['on_the_way', 'delivered'];

    public function index(): void
    {
        $target = !empty($_SESSION['rider_user_id']) ? 'rider/dashboard' : 'rider/login';
        $this->redirect($target);
    }

    public function login(): void
    {
        if (!empty($_SESSION['rider_user_id'])) {
            $this->redirect('rider/dashboard');
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
                $rider = $this->model('Rider')->findForLogin($old['email']);

                if (!$rider || !password_verify($password, $rider['password_hash'])) {
                    $errors[] = 'Invalid email or password.';
                } elseif ($rider['status'] !== 'active') {
                    $errors[] = 'This account is not active. Please contact support.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['rider_user_id'] = (int) $rider['user_id'];
                    $_SESSION['rider_id']      = (int) $rider['rider_id'];
                    $_SESSION['rider_name']    = $rider['first_name'] . ' ' . $rider['last_name'];
                    $this->redirect('rider/dashboard');
                }
            }
        }

        $this->view('rider/login', [
            'title'  => 'Rider Login - ordermo',
            'errors' => $errors,
            'old'    => $old,
        ], 'minimal');
    }

    public function logout(): void
    {
        unset(
            $_SESSION['rider_user_id'],
            $_SESSION['rider_id'],
            $_SESSION['rider_name']
        );
        $this->redirect('rider/login');
    }

    public function dashboard(): void
    {
        $this->requireRider();

        $riderModel = $this->model('Rider');
        $orderModel = $this->model('Order');
        $riderId    = (int) $_SESSION['rider_id'];

        $profile = $riderModel->profileByUserId((int) $_SESSION['rider_user_id']);
        if (!$profile) {
            $this->logout();
        }
        $approved = $profile['application_status'] === 'approved';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if ($action === 'toggle_available') {
                $available = (string) ($_POST['available'] ?? '') === '1';
                $riderModel->setAvailability($riderId, $available);
                $this->flashRedirect('rider/dashboard',
                    $available ? 'You are now available for deliveries.' : 'You are now offline.');
            }

            if ($action === 'order_status') {
                $orderId = (int) ($_POST['order_id'] ?? 0);
                $status  = (string) ($_POST['status'] ?? '');

                if (!$approved) {
                    $this->flashRedirect('rider/dashboard',
                        'Your rider account is still pending approval.');
                }
                if ($orderId > 0 && in_array($status, self::RIDER_STATUSES, true)
                    && $orderModel->updateStatus($orderId, $status)) {
                    $this->flashRedirect('rider/dashboard',
                        $status === 'delivered'
                            ? 'Order #' . $orderId . ' marked delivered. Nice work!'
                            : 'Order #' . $orderId . ' picked up — drive safe!');
                }
                $this->flashRedirect('rider/dashboard', 'Could not update that delivery.');
            }

            $this->flashRedirect('rider/dashboard', 'Unknown action.');
        }

        $this->view('rider/dashboard', [
            'title'        => 'Rider Dashboard - ordermo',
            'pageTitle'    => 'Dashboard',
            'portalRole'   => 'Rider',
            'portalName'   => $profile['first_name'] . ' ' . $profile['last_name'],
            'portalHome'   => 'rider/dashboard',
            'portalLogout' => 'rider/logout',
            'profile'      => $profile,
            'approved'     => $approved,
            'deliveries'   => $approved ? $orderModel->forDelivery() : [],
        ], 'portal');
    }

    public function apply(): void
    {
        $this->view('rider/apply', [
            'title' => 'Apply as Rider - ordermo',
        ]);
    }

    // --- helpers -----------------------------------------------------------

    private function requireRider(): void
    {
        if (empty($_SESSION['rider_user_id'])) {
            $this->redirect('rider/login');
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
