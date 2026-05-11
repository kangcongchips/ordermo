<?php $title = $title ?? APP_NAME; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>images/favicon.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body class="minimal-body">
    <header class="minimal-header">
        <a href="<?= BASE_URL ?>" class="logo">
            <img src="<?= BASE_URL ?>images/logo.svg" alt="ordermo">
        </a>
    </header>

    <main>
        <?= $content ?>
    </main>
</body>
</html>
