<div class="adm-card">
    <h2 class="adm-card-title">All orders (<?= count($orders) ?>)</h2>
    <?php if (!$orders): ?>
        <p class="adm-empty">No orders yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Customer</th><th>Merchant</th><th>Total</th><th>Placed</th><th>Status</th><th></th></tr>
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
                        <td>
                            <form action="<?= BASE_URL ?>admin/orders" method="post" class="adm-inline-form">
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
