<?php
/** @var array $items */
/** @var float $total */
?>
<section class="cart-section">
    <h1 class="cart-title">Your cart</h1>

    <?php if (empty($items)): ?>
        <div class="empty-state empty-state-page">
            <h2>Your cart is empty</h2>
            <p>Browse restaurants and add something delicious.</p>
            <a href="<?= BASE_URL ?>" class="btn-primary">Find food</a>
        </div>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($items as $item): ?>
                <article class="cart-item">
                    <?php if (!empty($item['image'])): ?>
                        <div class="cart-item-thumb"
                             style="background-image:url('<?= BASE_URL ?>images/food/<?= htmlspecialchars($item['image']) ?>')"></div>
                    <?php endif; ?>
                    <div class="cart-item-body">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="cart-item-meta"><?= htmlspecialchars($item['business_name']) ?></p>
                        <p class="cart-item-meta">
                            ₱<?= number_format((float) $item['price'], 2) ?> &times; <?= (int) $item['qty'] ?>
                        </p>
                    </div>
                    <div class="cart-item-right">
                        <span class="cart-item-subtotal">₱<?= number_format($item['subtotal'], 2) ?></span>
                        <form action="<?= BASE_URL ?>cart/remove" method="post">
                            <input type="hidden" name="menu_item_id" value="<?= (int) $item['id'] ?>">
                            <button type="submit" class="cart-remove">Remove</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="cart-total-row">
                <span>Total</span>
                <span class="cart-total">₱<?= number_format($total, 2) ?></span>
            </div>
            <a href="<?= BASE_URL ?>cart/checkout" class="btn-primary cart-checkout">Checkout</a>
        </div>
    <?php endif; ?>
</section>
