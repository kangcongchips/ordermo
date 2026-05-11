<section class="auth">
    <div class="auth-inner">
        <span class="auth-badge auth-badge-merchant">Merchant</span>
        <h1 class="auth-title">Welcome Back</h1>

        <form action="<?= BASE_URL ?>merchant/login" method="post" class="login-form">
            <input type="email" name="email" class="signup-input" placeholder="Email" required>
            <input type="password" name="password" class="signup-input" placeholder="Password" required>
            <button type="submit" class="btn-primary">Log In</button>
        </form>

        <p class="auth-signup">
            Don't have a merchant account?
            <a href="<?= BASE_URL ?>merchant/apply">Apply now</a>
        </p>

        <p class="auth-terms">
            By continuing, you agree to our
            <a href="#">Terms of Use</a> and
            <a href="#">Privacy Policy</a>.
        </p>
    </div>
</section>
