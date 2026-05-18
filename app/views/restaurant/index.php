<?php
/** @var array $restaurant */
/** @var array $menu */
$r        = $restaurant;
$open     = (int) $r['is_open'] === 1;
$priceTag = str_repeat('₱', max(1, min(4, (int) $r['price_level'])));
$cover    = $r['cover_image']
    ? BASE_URL . 'images/food/' . htmlspecialchars($r['cover_image'])
    : '';
?>
<section class="rest-banner" <?php if ($cover): ?>style="background-image:url('<?= $cover ?>')"<?php endif; ?>>
    <div class="rest-banner-overlay"></div>
    <div class="rest-banner-content">
        <a href="<?= BASE_URL ?>city/<?= (int) $r['city_id'] ?>" class="rest-back">
            &larr; <?= htmlspecialchars($r['city_name'] ?? 'Back') ?>
        </a>
        <h1><?= htmlspecialchars($r['business_name']) ?></h1>
        <div class="rest-banner-meta">
            <span class="price-level"><?= $priceTag ?></span>
            <span><?= htmlspecialchars($r['cuisine']) ?></span>
            <span class="rest-banner-rating">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/>
                </svg>
                <?= number_format((float) $r['rating'], 1) ?> (<?= (int) $r['rating_count'] ?>)
            </span>
        </div>
        <div class="rest-banner-tags">
            <span class="rest-pill"><?= (int) $r['prep_min'] ?>–<?= (int) $r['prep_max'] ?> mins</span>
            <?php if ((int) $r['free_delivery'] === 1): ?>
                <span class="rest-pill rest-pill-green">Free delivery</span>
            <?php endif; ?>
            <span class="rest-pill <?= $open ? 'rest-pill-green' : 'rest-pill-muted' ?>">
                <?= $open
                    ? 'Open now · until ' . date('g:i A', strtotime($r['closes_at']))
                    : 'Closed · opens ' . date('g:i A', strtotime($r['opens_at'])) ?>
            </span>
        </div>
        <p class="rest-address"><?= htmlspecialchars($r['business_address']) ?></p>
    </div>
</section>

<section class="menu-section">
    <?php if (empty($menu)): ?>
        <div class="empty-state">
            <h2>Menu coming soon</h2>
            <p>This restaurant hasn't published its menu yet.</p>
            <a href="<?= BASE_URL ?>city/<?= (int) $r['city_id'] ?>" class="btn-primary">
                Back to <?= htmlspecialchars($r['city_name'] ?? 'restaurants') ?>
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($menu as $category => $items): ?>
            <div class="menu-group">
                <h2 class="menu-category"><?= htmlspecialchars($category) ?></h2>
                <div class="menu-list">
                    <?php foreach ($items as $item): ?>
                        <article class="menu-item">
                            <div class="menu-item-body">
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="menu-item-desc"><?= htmlspecialchars($item['description']) ?></p>
                                <div class="menu-item-foot">
                                    <span class="menu-item-price">₱<?= number_format((float) $item['price'], 2) ?></span>
                                    <button type="button" class="menu-add" aria-label="Add to cart">Add +</button>
                                </div>
                            </div>
                            <?php if (!empty($item['image'])): ?>
                                <div class="menu-item-thumb"
                                     style="background-image:url('<?= BASE_URL ?>images/food/<?= htmlspecialchars($item['image']) ?>')"></div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
