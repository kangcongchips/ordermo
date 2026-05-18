<?php
/** @var array $city */
/** @var array $restaurants */
$priceTag = fn(int $n): string => str_repeat('₱', max(1, min(4, $n)));
?>
<section class="city-hero">
    <div class="city-hero-overlay"></div>
    <div class="city-hero-content">
        <span class="city-hero-eyebrow">Delivering to</span>
        <h1><?= htmlspecialchars($city['name']) ?></h1>
        <p class="city-hero-sub"><?= htmlspecialchars($city['province'] ?? '') ?></p>
    </div>
</section>

<section class="restaurant-section">
    <?php if (empty($restaurants)): ?>
        <div class="empty-state">
            <h2>No restaurants here yet</h2>
            <p>We're not delivering in <?= htmlspecialchars($city['name']) ?> just yet. Check back soon!</p>
            <a href="<?= BASE_URL ?>" class="btn-primary">Back to cities</a>
        </div>
    <?php else: ?>
        <div class="restaurant-section-head">
            <h2><?= count($restaurants) ?> place<?= count($restaurants) === 1 ? '' : 's' ?> to order from</h2>
            <p>Tap a restaurant to see its menu</p>
        </div>

        <div class="restaurant-grid">
            <?php foreach ($restaurants as $r): ?>
                <?php
                    $open  = (int) $r['is_open'] === 1;
                    $cover = $r['cover_image']
                        ? BASE_URL . 'images/food/' . htmlspecialchars($r['cover_image'])
                        : '';
                ?>
                <a href="<?= BASE_URL ?>restaurant/<?= (int) $r['id'] ?>"
                   class="restaurant-card<?= $open ? '' : ' is-closed' ?>">
                    <div class="restaurant-cover"
                         <?php if ($cover): ?>style="background-image:url('<?= $cover ?>')"<?php endif; ?>>
                        <?php if ((int) $r['free_delivery'] === 1): ?>
                            <span class="badge badge-free">FREE DELIVERY</span>
                        <?php endif; ?>
                        <?php if (!$open): ?>
                            <span class="restaurant-closed">
                                Opens <?= date('g:i A', strtotime($r['opens_at'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="restaurant-info">
                        <h3 class="restaurant-name"><?= htmlspecialchars($r['business_name']) ?></h3>
                        <div class="restaurant-meta">
                            <span class="price-level"><?= $priceTag((int) $r['price_level']) ?></span>
                            <?php foreach (array_filter(array_map('trim', explode(',', $r['cuisine']))) as $tag): ?>
                                <span class="cuisine-tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="restaurant-foot">
                            <span class="rest-time">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
                                </svg>
                                <?= (int) $r['prep_min'] ?> – <?= (int) $r['prep_max'] ?> mins
                            </span>
                            <span class="rest-rating">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/>
                                </svg>
                                <?= number_format((float) $r['rating'], 1) ?>
                                <span class="rest-rating-count">(<?= (int) $r['rating_count'] ?>)</span>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
