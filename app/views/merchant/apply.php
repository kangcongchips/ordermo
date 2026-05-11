<section class="auth">
    <div class="auth-inner">
        <span class="auth-badge auth-badge-merchant">Merchant</span>
        <h1 class="auth-title">Apply as Merchant</h1>

        <form action="<?= BASE_URL ?>merchant/apply" method="post" class="signup-form">
            <input type="text" name="first_name" class="signup-input" placeholder="First Name" required>
            <input type="text" name="last_name" class="signup-input" placeholder="Last Name" required>
            <input type="text" name="business_name" class="signup-input" placeholder="Business Name" required>
            <input type="text" name="business_address" class="signup-input" placeholder="Business Address" required>
            <input type="email" name="email" class="signup-input" placeholder="Email" required>

            <input type="tel" name="phone" class="signup-input" placeholder="Phone Number" inputmode="numeric" pattern="[0-9]*" required>

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
            Already a merchant?
            <a href="<?= BASE_URL ?>merchant/login">Log in</a>
        </p>
    </div>
</section>
