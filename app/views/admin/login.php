<section class="auth">
    <div class="auth-inner">
        <span class="auth-badge auth-badge-admin">Admin</span>
        <h1 class="auth-title">Admin Login</h1>

        <form action="<?= BASE_URL ?>admin/login" method="post" class="login-form">
            <input type="text" name="username" class="signup-input" placeholder="Username" required autofocus>
            <input type="password" name="password" class="signup-input" placeholder="Password" required>
            <button type="submit" class="btn-primary btn-admin">Log In</button>
        </form>
    </div>
</section>
