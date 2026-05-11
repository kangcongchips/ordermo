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
    <h2>Featured Cities</h2>
    <div class="city-grid">
        <?php foreach ($cities as $c): ?>
            <a href="<?= BASE_URL ?>city/<?= (int) $c['id'] ?>" class="city-card">
                <div class="city-image" style="background-image:url('<?= BASE_URL ?>images/<?= htmlspecialchars($c['image']) ?>')"></div>
                <span class="city-name"><?= htmlspecialchars($c['name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
