<section class="auth">
    <div class="auth-inner">
        <span class="auth-badge auth-badge-rider">Rider</span>
        <h1 class="auth-title">Welcome Back</h1>

        <?php if (!empty($errors)): ?>
            <div class="auth-errors">
                <?php foreach ($errors as $error): ?>
                    <p class="auth-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>rider/login" method="post" class="login-form">
            <input type="email" name="email" class="signup-input" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required autofocus>
            <input type="password" name="password" class="signup-input" placeholder="Password" required>
            <button type="submit" class="btn-primary">Log In</button>
        </form>

        <p class="auth-signup">
            Don't have a rider account?
            <a href="<?= BASE_URL ?>rider/apply">Apply now</a>
        </p>

        <p class="auth-terms">
            By continuing, you agree to our
            <a href="#">Terms of Use</a> and
            <a href="#">Privacy Policy</a>.
        </p>
    </div>
</section>
