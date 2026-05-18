<?php
/** @var array  $groups */
/** @var float  $grandTotal */
/** @var array  $old */
/** @var array  $errors */
?>
<section class="checkout-section">
    <h1 class="cart-title">Checkout</h1>

    <?php if (!empty($errors)): ?>
        <div class="auth-errors">
            <?php foreach ($errors as $error): ?>
                <p class="auth-error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="checkout-grid">
        <form action="<?= BASE_URL ?>cart/checkout" method="post" class="checkout-form">
            <h2 class="checkout-heading">Delivery details</h2>

            <label class="checkout-label">Delivery address</label>
            <textarea name="delivery_address" class="checkout-input" rows="2" required
                      placeholder="House / unit, street, barangay, city"><?= htmlspecialchars($old['delivery_address']) ?></textarea>

            <label class="checkout-label">Contact number</label>
            <input type="tel" name="contact_phone" class="checkout-input"
                   inputmode="numeric" pattern="[0-9]*" required
                   value="<?= htmlspecialchars($old['contact_phone']) ?>">

            <label class="checkout-label">Notes for the rider <span>(optional)</span></label>
            <input type="text" name="notes" class="checkout-input"
                   value="<?= htmlspecialchars($old['notes']) ?>"
                   placeholder="e.g. Leave at the gate">

            <label class="checkout-label">Payment method</label>
            <div class="checkout-payment">Cash on Delivery</div>

            <button type="submit" class="btn-primary checkout-place">
                Place order &middot; ₱<?= number_format($grandTotal, 2) ?>
            </button>
        </form>

        <aside class="checkout-summary">
            <h2 class="checkout-heading">Order summary</h2>

            <?php foreach ($groups as $group): ?>
                <div class="checkout-store">
                    <h3 class="checkout-store-name"><?= htmlspecialchars($group['business_name']) ?></h3>
                    <?php foreach ($group['items'] as $item): ?>
                        <div class="checkout-line">
                            <span><?= (int) $item['qty'] ?>&times; <?= htmlspecialchars($item['name']) ?></span>
                            <span>₱<?= number_format($item['subtotal'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="checkout-line checkout-line-muted">
                        <span>Subtotal</span>
                        <span>₱<?= number_format($group['subtotal'], 2) ?></span>
                    </div>
                    <div class="checkout-line checkout-line-muted">
                        <span>Delivery fee</span>
                        <span>
                            <?= $group['delivery_fee'] > 0
                                ? '₱' . number_format($group['delivery_fee'], 2)
                                : 'Free' ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="checkout-line checkout-grand">
                <span>Total</span>
                <span>₱<?= number_format($grandTotal, 2) ?></span>
            </div>
            <?php if (count($groups) > 1): ?>
                <p class="checkout-note">
                    Your items are from <?= count($groups) ?> restaurants, so they'll
                    be placed as <?= count($groups) ?> separate orders.
                </p>
            <?php endif; ?>
        </aside>
    </div>
</section>
