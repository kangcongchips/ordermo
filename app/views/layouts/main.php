<?php $title = $title ?? APP_NAME; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/favicon.png">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>images/favicon.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="navbar">
            <a href="<?= BASE_URL ?>" class="logo">
                <img src="<?= BASE_URL ?>images/logo.svg" alt="ordermo">
            </a>
            <nav class="nav-right">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <?php $initial = strtoupper(substr($_SESSION['user_name'] ?? '?', 0, 1)); ?>
                    <div class="dropdown">
                        <button class="dropdown-toggle user-avatar" aria-label="Account menu">
                            <span class="user-avatar-initial"><?= htmlspecialchars($initial) ?></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="<?= BASE_URL ?>auth/logout">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="dropdown-toggle">Account <span class="caret">▾</span></button>
                        <ul class="dropdown-menu">
                            <li><a href="<?= BASE_URL ?>auth/login">Log in / Sign up</a></li>
                            <li class="dropdown-divider"></li>
                            <li><a href="<?= BASE_URL ?>merchant/login">Merchant Login</a></li>
                            <li><a href="<?= BASE_URL ?>rider/login">Rider Login</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php $cartCount = array_sum($_SESSION['cart'] ?? []); ?>
                <a href="<?= BASE_URL ?>cart" class="cart-icon" aria-label="Cart">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-count"><?= (int) $cartCount ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="flash"><?= htmlspecialchars($_SESSION['flash']) ?></div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <img src="<?= BASE_URL ?>images/logo-white.svg" alt="ordermo">
                <p>Hassle-free Delivery Service</p>
            </div>
            <ul class="footer-links">
                <li><a href="#">Terms of Use</a></li>
                <li><a href="#">Data Privacy Policy</a></li>
            </ul>
            <ul class="footer-links">
                <li><a href="#">About Us</a></li>
                <li><a href="#">Frequently Asked Questions</a></li>
            </ul>
            <div class="footer-locations">
                <h4>Available Locations</h4>
                <ul>
                    <li><a href="#">Olongapo City</a></li>
                    <li><a href="#">Subic Bay Freeport</a></li>
                    <li><a href="#">Zambales</a></li>
                    <li><a href="#">Bataan</a></li>
                    <li><a href="#">Bulacan</a></li>
                    <li><a href="#">Pampanga</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2018 - <?= date('Y') ?> ordermo.ph | All Rights Reserved</p>
            <p>Made with <span class="heart">&#9829;</span> in Olongapo City</p>
        </div>
    </footer>

    <script src="<?= BASE_URL ?>js/app.js"></script>
</body>
</html>
