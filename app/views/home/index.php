<section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Get what you need,<br>i-ordermo na yan!</h1>
        <p>Skip the traffic, avoid the hassle - we bring convenience to you, fast!</p>

        <div class="steps">
            <div class="step">
                <div class="step-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M3 9l1-5h16l1 5"/>
                        <path d="M4 9v11h16V9"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <span class="step-label step-1">1. Choose</span>
            </div>
            <div class="step">
                <div class="step-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M2 17h20"/>
                        <path d="M4 17a8 8 0 0 1 16 0"/>
                        <circle cx="12" cy="6" r="1.5"/>
                    </svg>
                </div>
                <span class="step-label step-2">2. Order</span>
            </div>
            <div class="step">
                <div class="step-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <rect x="1" y="7" width="13" height="10"/>
                        <path d="M14 10h5l3 3v4h-8"/>
                        <circle cx="6" cy="19" r="2"/>
                        <circle cx="17" cy="19" r="2"/>
                    </svg>
                </div>
                <span class="step-label step-3">3. We Deliver</span>
            </div>
        </div>
    </div>
</section>

<section class="featured">
    <div class="featured-header">
        <h2>Featured Cities</h2>
        <p class="featured-sub">See available services in your area</p>
    </div>

    <?php foreach ($provinces as $province => $cities): ?>
        <div class="province-block">
            <h3 class="province-title"><?= htmlspecialchars($province) ?></h3>
            <div class="city-grid">
                <?php foreach ($cities as $c): ?>
                    <a href="<?= BASE_URL ?>city/<?= (int) $c['id'] ?>" class="city-card"
                       style="background-image:url('<?= BASE_URL ?>images/cities/<?= htmlspecialchars($c['image']) ?>')">
                        <span class="city-name"><?= htmlspecialchars($c['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<section class="easy-steps">
    <div class="easy-steps-inner">
        <h2>3 Easy Steps to Order!</h2>

        <div class="easy-steps-grid">
            <div class="easy-step">
                <div class="easy-step-icon">
                    <span class="easy-step-number">1</span>
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M3 9l1-5h16l1 5"/>
                        <path d="M4 9v11h16V9"/>
                        <path d="M9 22V12h6v10"/>
                    </svg>
                </div>
                <h3>Choose a restaurant...</h3>
                <p>We've got you covered with our partner restaurants around the city!</p>
            </div>

            <div class="easy-step">
                <div class="easy-step-icon">
                    <span class="easy-step-number">2</span>
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M2 17h20"/>
                        <path d="M4 17a8 8 0 0 1 16 0"/>
                        <circle cx="12" cy="6" r="1.5"/>
                    </svg>
                </div>
                <h3>Then choose a tasty dish...</h3>
                <p>Yes, it is that convenient!</p>
            </div>

            <div class="easy-step">
                <div class="easy-step-icon">
                    <span class="easy-step-number">3</span>
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <rect x="1" y="7" width="13" height="10"/>
                        <path d="M14 10h5l3 3v4h-8"/>
                        <circle cx="6" cy="19" r="2"/>
                        <circle cx="17" cy="19" r="2"/>
                    </svg>
                </div>
                <h3>...and We Deliver!</h3>
                <p>Goodbye traffic jam! Just wait for us and you will surely enjoy your favorite dish, hassle-free!</p>
            </div>
        </div>

        <p class="payment-note">We accept Cash on Delivery, Credit or Debit Card and GCash!</p>
    </div>
</section>

<section class="cta-rows">
    <div class="cta-row">
        <h3 class="cta-title">How ordermo works?</h3>
        <p class="cta-desc">Watch our short video on how you can place and track your orders.</p>
        <a href="#" class="cta-btn">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                <path d="M23 7s-.2-1.5-.8-2.2c-.8-.9-1.7-.9-2.1-1C17.1 3.5 12 3.5 12 3.5s-5.1 0-8.1.3c-.4.1-1.3.1-2.1 1C1.2 5.5 1 7 1 7S.8 8.8.8 10.6v1.7c0 1.8.2 3.6.2 3.6s.2 1.5.8 2.2c.8.9 1.9.9 2.4 1 1.7.2 7.8.3 7.8.3s5.1 0 8.1-.3c.4-.1 1.3-.1 2.1-1 .6-.7.8-2.2.8-2.2s.2-1.8.2-3.6v-1.7c0-1.8-.2-3.6-.2-3.6zM9.7 14.6V8.3l6.6 3.2-6.6 3.1z"/>
            </svg>
            Watch Video
        </a>
    </div>
    <div class="cta-row">
        <h3 class="cta-title">Have some questions?</h3>
        <p class="cta-desc">Visit our FAQs page and see if we have an answer to your questions.</p>
        <a href="#" class="cta-btn">Visit FAQs Page</a>
    </div>
    <div class="cta-row">
        <h3 class="cta-title">Are you a merchant?</h3>
        <p class="cta-desc">Join the growing number of merchants who benefit from having their menus listed at <span class="brand-text">ordermo</span>.</p>
        <a href="#" class="cta-btn">Become a Partner</a>
    </div>
</section>
