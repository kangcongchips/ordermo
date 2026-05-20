<div class="adm-card">
    <h2 class="adm-card-title">All orders (<?= count($orders) ?>)</h2>
    <p class="adm-form-note">
        Order status is managed by restaurants and riders from their own portals.
        This is a read-only view of every order across the platform.
    </p>
    <?php if (!$orders): ?>
        <p class="adm-empty">No orders yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Customer</th><th>Restaurant</th><th>Total</th><th>Placed</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= (int) $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td><?= htmlspecialchars($o['business_name']) ?></td>
                        <td>₱<?= number_format((float) $o['total'], 2) ?></td>
                        <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($o['created_at']))) ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars(str_replace('_', ' ', $o['status'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
