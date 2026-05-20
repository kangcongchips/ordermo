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
                } elseif ($rider['application_status'] === 'pending') {
                    $errors[] = 'Your application is still pending admin approval.';
                } elseif ($rider['application_status'] === 'rejected') {
                    $errors[] = 'Your application was rejected. Please contact support.';
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
            'stats'        => $approved ? $orderModel->riderBoardStats() : ['waiting' => 0, 'in_transit' => 0, 'delivered_today' => 0],
            'deliveries'   => $approved ? $orderModel->forDelivery() : [],
        ], 'portal');
    }

    public function apply(): void
    {
        $errors  = [];
        $success = false;
        $old     = [
            'first_name'     => '',
            'last_name'      => '',
            'email'          => '',
            'phone'          => '',
            'vehicle_type'   => '',
            'license_number' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'first_name'     => trim($_POST['first_name'] ?? ''),
                'last_name'      => trim($_POST['last_name'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
                'phone'          => trim($_POST['phone'] ?? ''),
                'vehicle_type'   => trim($_POST['vehicle_type'] ?? ''),
                'license_number' => trim($_POST['license_number'] ?? ''),
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
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }
            if (!preg_match('/^09\d{9}$/', $normalizedPhone)) {
                $errors[] = 'Enter a valid PH mobile number (e.g. 09171234567).';
            }
            if (!in_array($old['vehicle_type'], ['motorcycle', 'bicycle', 'car'], true)) {
                $errors[] = 'Choose a valid vehicle type.';
            }
            if ($old['license_number'] === '') {
                $errors[] = 'License number is required.';
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
                    $this->model('Rider')->create([
                        'first_name'         => $old['first_name'],
                        'last_name'          => $old['last_name'],
                        'email'              => $old['email'],
                        'phone'              => $normalizedPhone,
                        'password'           => $password,
                        'vehicle_type'       => $old['vehicle_type'],
                        'license_number'     => $old['license_number'],
                        'application_status' => 'pending',
                    ]);
                    $success = true;
                    $old = [
                        'first_name' => '', 'last_name' => '', 'email' => '',
                        'phone' => '', 'vehicle_type' => '', 'license_number' => '',
                    ];
                } catch (Throwable $e) {
                    $errors[] = 'Could not submit application. Please try again.';
                }
            }
        }

        $this->view('rider/apply', [
            'title'   => 'Apply as Rider - ordermo',
            'errors'  => $errors,
            'old'     => $old,
            'success' => $success,
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
