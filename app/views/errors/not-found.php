<?php /** @var string $message */ ?>
<section class="empty-state empty-state-page">
    <h2>404</h2>
    <p><?= htmlspecialchars($message ?? 'Page not found.') ?></p>
    <a href="<?= BASE_URL ?>" class="btn-primary">Back to home</a>
</section>
