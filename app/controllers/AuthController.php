<?php

class AuthController extends Controller
{
    public function login(): void
    {
        $errors = [];
        $old    = ['phone' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['phone'] = trim($_POST['phone'] ?? '');
            $password     = (string) ($_POST['password'] ?? '');

            if ($old['phone'] === '' || $password === '') {
                $errors[] = 'Phone number and password are required.';
            }

            if (!$errors) {
                $userModel      = $this->model('User');
                $normalizedPhone = User::normalizePhone($old['phone']);
                $user           = $userModel->findByPhoneAndRole($normalizedPhone, 'customer');

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $errors[] = 'Invalid phone number or password.';
                } elseif ($user['status'] !== 'active') {
                    $errors[] = 'Your account is not active. Please contact support.';
                } else {
                    $_SESSION['user_id']   = (int) $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['first_name'];
                    $_SESSION['flash']     = 'Welcome back, ' . $user['first_name'] . '!';

                    header('Location: ' . BASE_URL);
                    exit;
                }
            }
        }

        $this->view('auth/login', [
            'title'  => 'Log in / Sign up - ordermo',
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        header('Location: ' . BASE_URL);
        exit;
    }

    public function register(): void
    {
        $errors = [];
        $old = [
            'first_name' => '',
            'last_name'  => '',
            'email'      => '',
            'phone'      => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name'  => trim($_POST['last_name'] ?? ''),
                'email'      => trim($_POST['email'] ?? ''),
                'phone'      => trim($_POST['phone'] ?? ''),
            ];
            $password        = (string) ($_POST['password'] ?? '');
            $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

            if ($old['first_name'] === '') {
                $errors[] = 'First name is required.';
            }
            if ($old['last_name'] === '') {
                $errors[] = 'Last name is required.';
            }
            if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }
            $normalizedPhone = User::normalizePhone($old['phone']);
            if (!preg_match('/^09\d{9}$/', $normalizedPhone)) {
                $errors[] = 'Enter a valid PH mobile number (e.g. 09171234567).';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
            if ($password !== $passwordConfirm) {
                $errors[] = 'Passwords do not match.';
            }

            $userModel = $this->model('User');

            if (!$errors && $userModel->findByEmail($old['email'])) {
                $errors[] = 'That email is already registered.';
            }

            if (!$errors) {
                try {
                    $userId = $userModel->createCustomer(array_merge($old, [
                        'phone'    => $normalizedPhone,
                        'password' => $password,
                    ]));

                    $_SESSION['user_id']    = $userId;
                    $_SESSION['user_role']  = 'customer';
                    $_SESSION['user_name']  = $old['first_name'];
                    $_SESSION['flash']      = 'Welcome to ordermo, ' . $old['first_name'] . '!';

                    header('Location: ' . BASE_URL);
                    exit;
                } catch (Throwable $e) {
                    $errors[] = 'Sign up failed. Please try again.';
                }
            }
        }

        $this->view('auth/register', [
            'title'  => 'Sign up - ordermo',
            'errors' => $errors,
            'old'    => $old,
        ]);
    }
}
