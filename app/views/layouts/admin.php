<?php
$title  = $title ?? APP_NAME;
$active = $active ?? '';
$nav = [
    'dashboard' => ['Dashboard', 'admin/dashboard'],
    'riders'    => ['Riders',    'admin/riders'],
    'merchants' => ['Merchants', 'admin/merchants'],
    'products'  => ['Products',  'admin/products'],
    'customers' => ['Customers', 'admin/customers'],
    'orders'    => ['Orders',    'admin/orders'],
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
        <a href="<?= BASE_URL ?>admin/dashboard" class="adm-brand">
            <img src="<?= BASE_URL ?>images/logo-white.svg" alt="ordermo">
            <span class="adm-brand-tag">Admin</span>
        </a>
        <nav class="adm-nav">
            <?php foreach ($nav as $key => [$label, $path]): ?>
                <a href="<?= BASE_URL . $path ?>"
                   class="adm-nav-link<?= $active === $key ? ' is-active' : '' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <a href="<?= BASE_URL ?>admin/logout" class="adm-nav-link adm-nav-logout">Log out</a>
    </aside>

    <div class="adm-main">
        <?php $adminName = $_SESSION['admin_name'] ?? 'Admin'; ?>
        <header class="adm-topbar">
            <h1 class="adm-page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <div class="adm-user">
                <span class="adm-user-avatar"><?= htmlspecialchars(strtoupper(substr($adminName, 0, 1))) ?></span>
                <span class="adm-user-meta">
                    <span class="adm-user-name"><?= htmlspecialchars($adminName) ?></span>
                    <span class="adm-user-role">Administrator</span>
                </span>
            </div>
        </header>

        <main class="adm-content">
            <?php if (!empty($_SESSION['admin_flash'])): ?>
                <div class="adm-flash"><?= htmlspecialchars($_SESSION['admin_flash']) ?></div>
                <?php unset($_SESSION['admin_flash']); ?>
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
