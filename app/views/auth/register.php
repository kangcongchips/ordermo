<section class="auth">
    <div class="auth-inner">
        <h1 class="auth-title">Sign Up</h1>

        <?php if (!empty($errors)): ?>
            <div class="auth-errors">
                <?php foreach ($errors as $error): ?>
                    <p class="auth-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/register" method="post" class="signup-form">
            <input type="text" name="first_name" class="signup-input" placeholder="First Name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
            <input type="text" name="last_name" class="signup-input" placeholder="Last Name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
            <input type="email" name="email" class="signup-input" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>

            <input type="tel" name="phone" class="signup-input" placeholder="Phone Number" inputmode="numeric" pattern="[0-9]*" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>

            <input type="password" name="password" class="signup-input" placeholder="Password" minlength="8" required>
            <input type="password" name="password_confirm" class="signup-input" placeholder="Confirm Password" minlength="8" required>

            <p class="auth-terms signup-terms">
                By signing up, you are agreeing to our
                <a href="#">Terms of Use</a> and
                <a href="#">Data Privacy Policy</a>.
            </p>

            <button type="submit" class="btn-primary signup-submit">SIGN UP</button>
        </form>

        <p class="auth-signup">
            Already have an account?
            <a href="<?= BASE_URL ?>auth/login">Log in</a>
        </p>
    </div>
</section>
