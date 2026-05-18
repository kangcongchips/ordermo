<?php
$available = (int) $profile['is_available'] === 1;
?>
<?php if (!$approved): ?>
    <div class="portal-banner">
        Your rider account is
        <strong><?= htmlspecialchars($profile['application_status']) ?></strong>.
        You'll be able to accept deliveries once an admin approves it.
    </div>
<?php endif; ?>

<div class="adm-stats">
    <div class="adm-stat">
        <span class="adm-stat-num"><?= htmlspecialchars(ucfirst($profile['vehicle_type'])) ?></span>
        <span class="adm-stat-label">Vehicle</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num" style="font-size:1.1rem;"><?= htmlspecialchars($profile['license_number']) ?></span>
        <span class="adm-stat-label">License</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= $available ? 'Online' : 'Offline' ?></span>
        <span class="adm-stat-label">Availability</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= count($deliveries) ?></span>
        <span class="adm-stat-label">Open deliveries</span>
    </div>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">Your status</h2>
    <div class="portal-profile">
        <div>
            <span class="portal-profile-label">Rider</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Contact</span>
            <span class="portal-profile-value"><?= htmlspecialchars($profile['phone']) ?> · <?= htmlspecialchars($profile['email']) ?></span>
        </div>
        <div>
            <span class="portal-profile-label">Account</span>
            <span class="portal-profile-value">
                <span class="adm-badge adm-badge-<?= htmlspecialchars($profile['application_status']) ?>"><?= htmlspecialchars(ucfirst($profile['application_status'])) ?></span>
            </span>
        </div>
        <div>
            <span class="portal-profile-label">Availability</span>
            <span class="portal-profile-value">
                <span class="adm-badge <?= $available ? 'adm-badge-active' : 'adm-badge-cancelled' ?>"><?= $available ? 'Online' : 'Offline' ?></span>
                <?php if ($approved): ?>
                    <form action="<?= BASE_URL ?>rider/dashboard" method="post" class="portal-inline">
                        <input type="hidden" name="action" value="toggle_available">
                        <input type="hidden" name="available" value="<?= $available ? '0' : '1' ?>">
                        <button type="submit" class="adm-btn adm-btn-sm"><?= $available ? 'Go offline' : 'Go online' ?></button>
                    </form>
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">Delivery board (<?= count($deliveries) ?>)</h2>
    <?php if (!$approved): ?>
        <p class="adm-empty">Deliveries open up once your account is approved.</p>
    <?php elseif (!$deliveries): ?>
        <p class="adm-empty">No deliveries right now. Check back soon.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Pick up from</th><th>Deliver to</th><th>Total</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($deliveries as $d): ?>
                    <tr>
                        <td>#<?= (int) $d['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($d['business_name']) ?><br>
                            <span class="adm-muted"><?= htmlspecialchars($d['business_address']) ?></span>
                        </td>
                        <td>
                            <?= htmlspecialchars($d['customer_name']) ?><br>
                            <span class="adm-muted"><?= htmlspecialchars($d['delivery_address']) ?> · <?= htmlspecialchars($d['contact_phone']) ?></span>
                        </td>
                        <td>₱<?= number_format((float) $d['total'], 2) ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($d['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $d['status'])) ?></span></td>
                        <td>
                            <form action="<?= BASE_URL ?>rider/dashboard" method="post" class="portal-inline">
                                <input type="hidden" name="action" value="order_status">
                                <input type="hidden" name="order_id" value="<?= (int) $d['id'] ?>">
                                <?php if ($d['status'] === 'preparing'): ?>
                                    <input type="hidden" name="status" value="on_the_way">
                                    <button type="submit" class="adm-btn adm-btn-sm">Pick up</button>
                                <?php else: ?>
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="adm-btn adm-btn-sm">Mark delivered</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
