<?php
$isApproved = $profile['application_status'] === 'approved';
$isOpen     = (int) $profile['is_open'] === 1;
?>
<?php if (!$isApproved): ?>
    <div class="portal-banner">
        Your restaurant listing is
        <strong><?= htmlspecialchars($profile['application_status']) ?></strong>.
        Customers won't see it until an admin approves it.
    </div>
<?php endif; ?>

<div class="adm-stats">
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['orders'] ?></span>
        <span class="adm-stat-label">Total orders</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['pending'] ?></span>
        <span class="adm-stat-label">Pending</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['menu_items'] ?></span>
        <span class="adm-stat-label">Menu items</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num">₱<?= number_format((float) $stats['revenue'], 0) ?></span>
        <span class="adm-stat-label">Delivered revenue</span>
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
        <div>
            <span class="portal-profile-label">Status</span>
            <span class="portal-profile-value">
                <span class="adm-badge <?= $isOpen ? 'adm-badge-active' : 'adm-badge-cancelled' ?>"><?= $isOpen ? 'Open' : 'Closed' ?></span>
                <?php if ($isApproved): ?>
                    <form action="<?= BASE_URL ?>merchant/dashboard" method="post" class="portal-inline">
                        <input type="hidden" name="action" value="toggle_open">
                        <input type="hidden" name="open" value="<?= $isOpen ? '0' : '1' ?>">
                        <button type="submit" class="adm-btn adm-btn-sm"><?= $isOpen ? 'Close restaurant' : 'Open restaurant' ?></button>
                    </form>
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">Orders (<?= count($orders) ?>)</h2>
    <?php if (!$orders): ?>
        <p class="adm-empty">No orders yet. They'll show up here once customers start ordering.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Customer</th><th>Items total</th><th>Deliver to</th><th>Placed</th><th>Status</th><th>Update</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= (int) $o['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($o['customer_name']) ?><br>
                            <span class="adm-muted"><?= htmlspecialchars($o['contact_phone']) ?></span>
                        </td>
                        <td>
                            ₱<?= number_format((float) $o['total'], 2) ?><br>
                            <span class="adm-muted">incl. ₱<?= number_format((float) $o['delivery_fee'], 2) ?> delivery</span>
                        </td>
                        <td><span class="adm-muted"><?= htmlspecialchars($o['delivery_address']) ?></span></td>
                        <td><?= htmlspecialchars(date('M j, g:i A', strtotime($o['created_at']))) ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $o['status'])) ?></span></td>
                        <td>
                            <form action="<?= BASE_URL ?>merchant/dashboard" method="post" class="adm-inline-form">
                                <input type="hidden" name="action" value="order_status">
                                <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>">
                                <select name="status">
                                    <?php foreach ($statuses as $s): ?>
                                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= htmlspecialchars(str_replace('_', ' ', $s)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="adm-btn adm-btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
