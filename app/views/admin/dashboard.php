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
    <h2 class="adm-card-title">Pending restaurant applications (<?= count($pendingMerchants) ?>)</h2>
    <?php if (!$pendingMerchants): ?>
        <p class="adm-empty">No pending restaurant applications.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Restaurant</th><th>Owner</th><th>Contact</th><th>Submitted</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($pendingMerchants as $m): ?>
                    <tr>
                        <td><?= (int) $m['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($m['business_name']) ?><br>
                            <span class="adm-muted"><?= htmlspecialchars($m['business_address']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($m['phone']) ?><br>
                            <span class="adm-muted"><?= htmlspecialchars($m['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($m['created_at']))) ?></td>
                        <td>
                            <div class="adm-inline-form">
                                <form action="<?= BASE_URL ?>admin/merchants" method="post">
                                    <input type="hidden" name="merchant_id" value="<?= (int) $m['id'] ?>">
                                    <input type="hidden" name="application_status" value="approved">
                                    <button type="submit" class="adm-btn-icon adm-btn-approve" title="Approve" aria-label="Approve">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                    </button>
                                </form>
                                <form action="<?= BASE_URL ?>admin/merchants" method="post">
                                    <input type="hidden" name="merchant_id" value="<?= (int) $m['id'] ?>">
                                    <input type="hidden" name="application_status" value="rejected">
                                    <button type="submit" class="adm-btn-icon adm-btn-reject" title="Reject" aria-label="Reject">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">Pending rider applications (<?= count($pendingRiders) ?>)</h2>
    <?php if (!$pendingRiders): ?>
        <p class="adm-empty">No pending rider applications.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Contact</th><th>Vehicle</th><th>License</th><th>Submitted</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($pendingRiders as $r): ?>
                    <tr>
                        <td><?= (int) $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($r['phone']) ?><br>
                            <span class="adm-muted"><?= htmlspecialchars($r['email']) ?></span>
                        </td>
                        <td><?= htmlspecialchars(ucfirst($r['vehicle_type'])) ?></td>
                        <td><?= htmlspecialchars($r['license_number']) ?></td>
                        <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($r['created_at']))) ?></td>
                        <td>
                            <div class="adm-inline-form">
                                <form action="<?= BASE_URL ?>admin/riders" method="post">
                                    <input type="hidden" name="rider_id" value="<?= (int) $r['id'] ?>">
                                    <input type="hidden" name="application_status" value="approved">
                                    <button type="submit" class="adm-btn-icon adm-btn-approve" title="Approve" aria-label="Approve">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                    </button>
                                </form>
                                <form action="<?= BASE_URL ?>admin/riders" method="post">
                                    <input type="hidden" name="rider_id" value="<?= (int) $r['id'] ?>">
                                    <input type="hidden" name="application_status" value="rejected">
                                    <button type="submit" class="adm-btn-icon adm-btn-reject" title="Reject" aria-label="Reject">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
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
