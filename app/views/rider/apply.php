<section class="auth">
    <div class="auth-inner">
        <span class="auth-badge auth-badge-rider">Rider</span>
        <h1 class="auth-title">Apply as Rider</h1>

        <?php if (!empty($success)): ?>
            <div class="auth-errors" style="background:#e7f7ec;border-color:#bce5c8;color:#1f6b3a;">
                <p class="auth-error">Application submitted! An admin will review and approve your account shortly.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="auth-errors">
                <?php foreach ($errors as $error): ?>
                    <p class="auth-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>rider/apply" method="post" class="signup-form">
            <input type="text" name="first_name" class="signup-input" placeholder="First Name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
            <input type="text" name="last_name" class="signup-input" placeholder="Last Name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
            <input type="email" name="email" class="signup-input" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>

            <input type="tel" name="phone" class="signup-input" placeholder="Phone Number" inputmode="numeric" pattern="[0-9]*" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>

            <select name="vehicle_type" class="signup-input signup-select" required>
                <option value="" disabled <?= ($old['vehicle_type'] ?? '') === '' ? 'selected' : '' ?>>Vehicle Type</option>
                <?php foreach (['motorcycle', 'bicycle', 'car'] as $v): ?>
                    <option value="<?= $v ?>" <?= ($old['vehicle_type'] ?? '') === $v ? 'selected' : '' ?>><?= ucfirst($v) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="license_number" class="signup-input" placeholder="Driver's License Number" value="<?= htmlspecialchars($old['license_number'] ?? '') ?>" required>

            <input type="password" name="password" class="signup-input" placeholder="Password" minlength="8" required>
            <input type="password" name="password_confirm" class="signup-input" placeholder="Confirm Password" minlength="8" required>

            <p class="auth-terms signup-terms">
                By applying, you are agreeing to our
                <a href="#">Terms of Use</a> and
                <a href="#">Data Privacy Policy</a>.
            </p>

            <button type="submit" class="btn-primary signup-submit">SUBMIT APPLICATION</button>
        </form>

        <p class="auth-signup">
            Already a rider?
            <a href="<?= BASE_URL ?>rider/login">Log in</a>
        </p>
    </div>
</section>
