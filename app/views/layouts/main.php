<?php $title = $title ?? APP_NAME; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <header class="navbar">
        <a href="<?= BASE_URL ?>" class="logo">ordermo</a>
        <nav class="nav-right">
            <div class="dropdown">
                <button class="dropdown-toggle">Account <span class="caret">▾</span></button>
                <ul class="dropdown-menu">
                    <li><a href="<?= BASE_URL ?>auth/login">Login</a></li>
                    <li><a href="<?= BASE_URL ?>auth/register">Register</a></li>
                </ul>
            </div>
            <a href="<?= BASE_URL ?>cart" class="cart-icon" aria-label="Cart">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <script src="<?= BASE_URL ?>js/app.js"></script>
</body>
</html>
