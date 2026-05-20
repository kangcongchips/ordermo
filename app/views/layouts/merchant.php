<?php
$title        = $title ?? APP_NAME;
$active       = $active ?? '';
$merchantName = $_SESSION['merchant_name'] ?? 'Restaurant';
$nav = [
    'dashboard' => ['Dashboard', 'merchant/dashboard'],
    'orders'    => ['Orders',    'merchant/orders'],
    'products'  => ['Products',  'merchant/products'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/favicon.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body class="adm-body">
    <aside class="adm-sidebar">
        <a href="<?= BASE_URL ?>merchant/dashboard" class="adm-brand">
            <img src="<?= BASE_URL ?>images/logo-white.svg" alt="ordermo">
            <span class="adm-brand-tag">Restaurant</span>
        </a>
        <nav class="adm-nav">
            <?php foreach ($nav as $key => [$label, $path]): ?>
                <a href="<?= BASE_URL . $path ?>"
                   class="adm-nav-link<?= $active === $key ? ' is-active' : '' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <a href="<?= BASE_URL ?>merchant/logout" class="adm-nav-link adm-nav-logout">Log out</a>
    </aside>

    <div class="adm-main">
        <header class="adm-topbar">
            <h1 class="adm-page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <div class="adm-user">
                <span class="adm-user-avatar"><?= htmlspecialchars(strtoupper(substr($merchantName, 0, 1))) ?></span>
                <span class="adm-user-meta">
                    <span class="adm-user-name"><?= htmlspecialchars($merchantName) ?></span>
                    <span class="adm-user-role">Restaurant partner</span>
                </span>
            </div>
        </header>

        <main class="adm-content">
            <?php if (!empty($_SESSION['portal_flash'])): ?>
                <?php
                $flashMsg   = $_SESSION['portal_flash'];
                $flashLower = strtolower($flashMsg);
                $isError    = str_contains($flashLower, 'could not')
                    || str_contains($flashLower, 'failed')
                    || str_contains($flashLower, 'required')
                    || str_contains($flashLower, 'invalid')
                    || str_contains($flashLower, 'please');
                ?>
                <div class="adm-flash<?= $isError ? ' adm-flash-error' : '' ?>">
                    <?= htmlspecialchars($flashMsg) ?>
                </div>
                <?php unset($_SESSION['portal_flash']); ?>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="adm-errors">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</body>
</html>
