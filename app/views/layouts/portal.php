<?php
$title      = $title ?? APP_NAME;
$portalRole = $portalRole ?? 'Partner';
$portalName = $portalName ?? $portalRole;
$portalHome = $portalHome ?? '';
$portalLogout = $portalLogout ?? '';
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
<body class="adm-body portal-body">
    <div class="adm-main">
        <header class="adm-topbar portal-topbar">
            <a href="<?= BASE_URL . $portalHome ?>" class="portal-brand">
                <img src="<?= BASE_URL ?>images/logo.svg" alt="ordermo">
                <span class="adm-brand-tag"><?= htmlspecialchars($portalRole) ?></span>
            </a>
            <h1 class="adm-page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <div class="portal-spacer"></div>
            <div class="adm-user">
                <span class="adm-user-avatar"><?= htmlspecialchars(strtoupper(substr($portalName, 0, 1))) ?></span>
                <span class="adm-user-meta">
                    <span class="adm-user-name"><?= htmlspecialchars($portalName) ?></span>
                    <span class="adm-user-role"><?= htmlspecialchars($portalRole) ?> partner</span>
                </span>
            </div>
            <a href="<?= BASE_URL . $portalLogout ?>" class="portal-logout">Log out</a>
        </header>

        <main class="adm-content">
            <?php if (!empty($_SESSION['portal_flash'])): ?>
                <div class="adm-flash"><?= htmlspecialchars($_SESSION['portal_flash']) ?></div>
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
