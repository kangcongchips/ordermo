<?php
$isApproved = $profile['application_status'] === 'approved';
$isOpen     = (int) $profile['is_open'] === 1;
$needsSetup = $isApproved
    && (empty($profile['city_id']) || trim((string) $profile['cuisine']) === '');
$ownerFirst = trim((string) ($profile['first_name'] ?? '')) ?: $profile['business_name'];

$cuisineSuggestions = ['Filipino', 'Grill', 'Pasta', 'Chicken', 'Rice meals',
    'Seafood', 'Snacks', 'Desserts', 'Drinks'];
?>
<?php if (!$isApproved): ?>
    <div class="portal-banner">
        Your restaurant listing is
        <strong><?= htmlspecialchars($profile['application_status']) ?></strong>.
        Customers won't see it until an admin approves it.
    </div>
<?php endif; ?>

<section class="mdash-hero">
    <div class="mdash-hero-text">
        <span class="mdash-hero-greeting">Welcome back,</span>
        <h2 class="mdash-hero-name"><?= htmlspecialchars($ownerFirst) ?> 👋</h2>
        <p class="mdash-hero-sub">
            <?php if (!$isApproved): ?>
                We'll send you a heads-up the moment your listing goes live.
            <?php elseif ($isOpen): ?>
                You're accepting orders right now. Have a great service!
            <?php else: ?>
                You're paused. Flip the switch when you're ready to take orders.
            <?php endif; ?>
        </p>
    </div>

    <div class="mdash-hero-actions">
        <a href="<?= BASE_URL ?>merchant/products?add=1" class="mdash-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add product
        </a>

        <?php if ($isApproved): ?>
            <form action="<?= BASE_URL ?>merchant/dashboard" method="post" class="mdash-toggle">
                <input type="hidden" name="action" value="toggle_open">
                <input type="hidden" name="open" value="<?= $isOpen ? '0' : '1' ?>">
                <span class="mdash-toggle-label">
                    <span class="mdash-toggle-title">Accepting orders</span>
                    <span class="mdash-toggle-state mdash-toggle-state-<?= $isOpen ? 'on' : 'off' ?>">
                        <?= $isOpen ? 'On' : 'Paused' ?>
                    </span>
                </span>
                <button type="submit" class="mdash-switch <?= $isOpen ? 'is-on' : '' ?>" aria-label="Toggle order acceptance">
                    <span class="mdash-switch-dot"></span>
                </button>
            </form>
        <?php endif; ?>
    </div>
</section>

<div class="mdash-stats">
    <div class="mdash-stat mdash-stat-orders">
        <span class="mdash-stat-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 7h18l-1.5 12a2 2 0 0 1-2 1.8H6.5a2 2 0 0 1-2-1.8L3 7Z"/>
                <path d="M8 7V5a4 4 0 1 1 8 0v2"/>
            </svg>
        </span>
        <div>
            <span class="mdash-stat-num"><?= (int) $stats['orders'] ?></span>
            <span class="mdash-stat-label">Total orders</span>
        </div>
    </div>
    <div class="mdash-stat mdash-stat-pending">
        <span class="mdash-stat-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3 2"/>
            </svg>
        </span>
        <div>
            <span class="mdash-stat-num"><?= (int) $stats['pending'] ?></span>
            <span class="mdash-stat-label">Pending</span>
        </div>
    </div>
    <div class="mdash-stat mdash-stat-menu">
        <span class="mdash-stat-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16v16H4z"/>
                <path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>
        </span>
        <div>
            <span class="mdash-stat-num"><?= (int) $stats['menu_items'] ?></span>
            <span class="mdash-stat-label">Menu items</span>
        </div>
    </div>
    <div class="mdash-stat mdash-stat-revenue">
        <span class="mdash-stat-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20"/>
                <path d="M17 6H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/>
            </svg>
        </span>
        <div>
            <span class="mdash-stat-num">₱<?= number_format((float) $stats['revenue'], 0) ?></span>
            <span class="mdash-stat-label">Delivered revenue</span>
        </div>
    </div>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">Restaurant profile</h2>
    <div class="portal-profile">
        <div>
            <span class="portal-profile-label">Name</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['business_name']) ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Address</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['business_address']) ?></span>
        </div>
        <div>
            <span class="portal-profile-label">City</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['city_name'] ?? '—') ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Cuisine</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['cuisine'] !== '' ? $profile['cuisine'] : '—') ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Owner</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Contact</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['phone']) ?> · <?= htmlspecialchars($profile['email']) ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Listing</span>
            <span class="portal-profile-value">
                <span class="adm-badge adm-badge-<?= htmlspecialchars($profile['application_status']) ?>"><?= htmlspecialchars(ucfirst($profile['application_status'])) ?></span>
            </span>
        </div>
    </div>
</div>

<?php if ($needsSetup): ?>
    <div class="mdash-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="mdash-setup-title">
        <form action="<?= BASE_URL ?>merchant/dashboard" method="post" class="mdash-modal" enctype="multipart/form-data">
            <input type="hidden" name="action" value="setup_profile">

            <header class="mdash-modal-head">
                <span class="mdash-modal-step">One quick step</span>
                <h2 id="mdash-setup-title" class="mdash-modal-title">Finish setting up your restaurant</h2>
                <p class="mdash-modal-sub">
                    Pick your city, cuisine, and a cover photo so customers can discover
                    <strong><?= htmlspecialchars($profile['business_name']) ?></strong> on ordermo.
                </p>
            </header>

            <div class="mdash-modal-body">
                <label class="mdash-field">
                    <span class="mdash-field-label">City</span>
                    <select name="city_id" required>
                        <option value="">Select a city…</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?= (int) $c['id'] ?>"
                                <?= (int) ($profile['city_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                                <?= !empty($c['province']) ? ' — ' . htmlspecialchars($c['province']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="mdash-field">
                    <span class="mdash-field-label">Cuisine</span>
                    <input type="text" name="cuisine" placeholder="e.g. Filipino, Grill"
                           value="<?= htmlspecialchars($profile['cuisine'] ?? '') ?>"
                           required>
                    <span class="mdash-field-hint">Comma-separated. Choose up to a few that describe your menu best.</span>
                    <div class="mdash-chips">
                        <?php foreach ($cuisineSuggestions as $tag): ?>
                            <button type="button" class="mdash-chip" data-cuisine="<?= htmlspecialchars($tag) ?>">
                                <?= htmlspecialchars($tag) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </label>

                <label class="mdash-field">
                    <span class="mdash-field-label">Cover photo <span class="mdash-field-optional">Optional</span></span>
                    <span class="mdash-upload">
                        <span class="mdash-upload-ico" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path d="M21 15l-5-5L5 21"/>
                            </svg>
                        </span>
                        <span class="mdash-upload-text">
                            <strong>Click to upload a cover photo</strong>
                            <small>JPG, PNG, WEBP or GIF — up to 5&nbsp;MB</small>
                        </span>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp,image/gif">
                    </span>
                </label>
            </div>

            <footer class="mdash-modal-foot">
                <span class="mdash-modal-note">Required — you can change these later.</span>
                <button type="submit" class="adm-btn">Save and continue</button>
            </footer>
        </form>
    </div>

    <script>
        (function () {
            var input = document.querySelector('.mdash-modal input[name="cuisine"]');
            if (!input) return;
            document.querySelectorAll('.mdash-chip').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var tag = btn.getAttribute('data-cuisine');
                    var parts = input.value.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                    if (parts.indexOf(tag) === -1) parts.push(tag);
                    input.value = parts.join(', ');
                    input.focus();
                });
            });
        })();
    </script>
<?php endif; ?>
