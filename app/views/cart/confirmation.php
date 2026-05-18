<?php
/** @var array $orders */
/** @var float $grandTotal */
?>
<section class="checkout-section">
    <div class="confirm-head">
        <div class="confirm-check">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 9 17l-5-5"/>
            </svg>
        </div>
        <h1 class="cart-title">Order placed!</h1>
        <p class="confirm-sub">
            Thank you. <?= count($orders) > 1
                ? count($orders) . ' orders are' : 'Your order is' ?>
            being sent to the restaurant<?= count($orders) > 1 ? 's' : '' ?>.
        </p>
    </div>

    <?php foreach ($orders as $order): ?>
        <article class="confirm-order">
            <div class="confirm-order-top">
                <div>
                    <h3><?= htmlspecialchars($order['business_name']) ?></h3>
                    <p class="cart-item-meta">Order #<?= (int) $order['id'] ?></p>
                </div>
                <span class="confirm-status">
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                </span>
            </div>

            <?php foreach ($order['items'] as $item): ?>
                <div class="checkout-line">
                    <span><?= (int) $item['quantity'] ?>&times; <?= htmlspecialchars($item['name']) ?></span>
                    <span>₱<?= number_format((float) $item['subtotal'], 2) ?></span>
                </div>
            <?php endforeach; ?>

            <div class="checkout-line checkout-line-muted">
                <span>Delivery fee</span>
                <span>
                    <?= (float) $order['delivery_fee'] > 0
                        ? '₱' . number_format((float) $order['delivery_fee'], 2)
                        : 'Free' ?>
                </span>
            </div>
            <div class="checkout-line checkout-grand">
                <span>Order total</span>
                <span>₱<?= number_format((float) $order['total'], 2) ?></span>
            </div>

            <p class="cart-item-meta confirm-deliver">
                Deliver to: <?= htmlspecialchars($order['delivery_address']) ?>
                &middot; <?= htmlspecialchars($order['contact_phone']) ?>
                &middot; Cash on Delivery
            </p>
        </article>
    <?php endforeach; ?>

    <div class="checkout-line checkout-grand confirm-grand">
        <span>Grand total</span>
        <span>₱<?= number_format($grandTotal, 2) ?></span>
    </div>

    <a href="<?= BASE_URL ?>" class="btn-primary checkout-place">Back to home</a>
</section>
