<div class="adm-stats">
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['customers'] ?></span>
        <span class="adm-stat-label">Customers</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['merchants'] ?></span>
        <span class="adm-stat-label">Restaurants</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['riders'] ?></span>
        <span class="adm-stat-label">Riders</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat-num"><?= (int) $stats['orders'] ?></span>
        <span class="adm-stat-label">Orders</span>
    </div>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">Recent orders</h2>
    <?php if (!$recent): ?>
        <p class="adm-empty">No orders yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Customer</th><th>Restaurant</th><th>Total</th><th>Status</th><th>Placed</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $o): ?>
                    <tr>
                        <td>#<?= (int) $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td><?= htmlspecialchars($o['business_name']) ?></td>
                        <td>₱<?= number_format((float) $o['total'], 2) ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $o['status'])) ?></span></td>
                        <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($o['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
