<?php
$totalOrders = count($orders);
$pending     = 0;
$preparing   = 0;
$onTheWay    = 0;
$unassigned  = 0;
foreach ($orders as $o) {
    if ($o['status'] === 'pending')    $pending++;
    if ($o['status'] === 'preparing')  $preparing++;
    if ($o['status'] === 'on_the_way') $onTheWay++;
    if (empty($o['rider_id']) && !in_array($o['status'], ['delivered', 'cancelled'], true)) {
        $unassigned++;
    }
}
?>
<div class="mdash-page-head">
    <div>
        <h2 class="mdash-page-title">Orders <span class="mdash-page-count">(<?= $totalOrders ?>)</span></h2>
        <p class="mdash-page-sub">Manage incoming orders and pick the rider who'll deliver them.</p>
    </div>
    <?php if ($totalOrders): ?>
        <div class="mdash-pill-stats">
            <span class="mdash-pill mdash-pill-warn"><?= $pending ?> pending</span>
            <span class="mdash-pill mdash-pill-info"><?= $preparing ?> preparing</span>
            <span class="mdash-pill mdash-pill-blue"><?= $onTheWay ?> on the way</span>
            <?php if ($unassigned > 0): ?>
                <span class="mdash-pill mdash-pill-red"><?= $unassigned ?> need rider</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div class="adm-card">
    <?php if (!$orders): ?>
        <div class="mdash-empty">
            <span class="mdash-empty-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7h18l-1.5 12a2 2 0 0 1-2 1.8H6.5a2 2 0 0 1-2-1.8L3 7Z"/>
                    <path d="M8 7V5a4 4 0 1 1 8 0v2"/>
                    <path d="M9 12h6"/>
                </svg>
            </span>
            <p class="mdash-empty-title">No orders yet</p>
            <p class="mdash-empty-sub">When customers place an order, it'll appear here so you can hand it off to a rider.</p>
        </div>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Customer</th><th>Items total</th><th>Deliver to</th><th>Placed</th><th>Status</th><th>Rider</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <?php
                    $hasRider     = !empty($o['rider_id']);
                    $isFinished   = in_array($o['status'], ['delivered', 'cancelled'], true);
                    $canReassign  = !$isFinished;
                    $ridersJson   = htmlspecialchars(json_encode([
                        'order_id'   => (int) $o['id'],
                        'rider_id'   => $hasRider ? (int) $o['rider_id'] : null,
                        'rider_name' => $hasRider ? $o['rider_name'] : null,
                    ]), ENT_QUOTES);
                    ?>
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
                            <?php if ($isFinished): ?>
                                <?php if ($hasRider): ?>
                                    <span class="mdash-rider-tag"><?= htmlspecialchars($o['rider_name']) ?></span>
                                <?php else: ?>
                                    <span class="adm-muted">—</span>
                                <?php endif; ?>
                            <?php elseif ($hasRider): ?>
                                <div class="mdash-rider-cell">
                                    <span class="mdash-rider-tag"><?= htmlspecialchars($o['rider_name']) ?></span>
                                    <button type="button" class="adm-btn adm-btn-sm mdash-rider-change"
                                            data-assign-rider="<?= $ridersJson ?>">
                                        Change
                                    </button>
                                </div>
                            <?php else: ?>
                                <button type="button" class="adm-btn adm-btn-sm"
                                        data-assign-rider="<?= $ridersJson ?>">
                                    Assign rider
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="mdash-modal-overlay" data-modal="assign-rider" role="dialog" aria-modal="true" aria-labelledby="assign-rider-title" hidden>
    <form action="<?= BASE_URL ?>merchant/orders" method="post" class="mdash-modal mdash-modal-wide">
        <input type="hidden" name="action" value="assign_rider">
        <input type="hidden" name="order_id" id="assign-rider-order-id" value="">

        <button type="button" class="mdash-modal-close" data-modal-close aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <header class="mdash-modal-head">
            <span class="mdash-modal-step">Order <span id="assign-rider-order-label">#—</span></span>
            <h2 id="assign-rider-title" class="mdash-modal-title">Choose a rider</h2>
            <p class="mdash-modal-sub" id="assign-rider-current">
                Pick a rider to deliver this order. Online riders are listed first.
            </p>
        </header>

        <div class="mdash-modal-body">
            <?php if (!$riders): ?>
                <div class="mdash-empty" style="padding:1.25rem 0;">
                    <p class="mdash-empty-title">No approved riders yet</p>
                    <p class="mdash-empty-sub">Once an admin approves a rider, they'll show up here.</p>
                </div>
            <?php else: ?>
                <div class="mdash-rider-list">
                    <?php foreach ($riders as $r): ?>
                        <?php $name = trim($r['first_name'] . ' ' . $r['last_name']); ?>
                        <label class="mdash-rider-option">
                            <input type="radio" name="rider_id" value="<?= (int) $r['id'] ?>" required>
                            <span class="mdash-rider-avatar"><?= htmlspecialchars(strtoupper(substr($name, 0, 1))) ?></span>
                            <span class="mdash-rider-info">
                                <span class="mdash-rider-name"><?= htmlspecialchars($name) ?></span>
                                <span class="mdash-rider-meta">
                                    <?= htmlspecialchars(ucfirst($r['vehicle_type'])) ?>
                                    · <?= htmlspecialchars($r['phone']) ?>
                                </span>
                            </span>
                            <span class="mdash-rider-status mdash-rider-status-<?= (int) $r['is_available'] === 1 ? 'on' : 'off' ?>">
                                <?= (int) $r['is_available'] === 1 ? 'Online' : 'Offline' ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <footer class="mdash-modal-foot">
            <button type="button" class="mdash-modal-cancel" data-modal-close>Cancel</button>
            <button type="submit" class="adm-btn" <?= $riders ? '' : 'disabled' ?>>Assign rider</button>
        </footer>
    </form>
</div>

<script>
    (function () {
        var modal       = document.querySelector('[data-modal="assign-rider"]');
        if (!modal) return;
        var orderIdHid  = document.getElementById('assign-rider-order-id');
        var orderLabel  = document.getElementById('assign-rider-order-label');
        var currentLine = document.getElementById('assign-rider-current');

        var open = function (payload) {
            orderIdHid.value = payload.order_id;
            orderLabel.textContent = '#' + payload.order_id;
            currentLine.textContent = payload.rider_name
                ? 'Currently assigned to ' + payload.rider_name + ' — pick someone else below.'
                : 'Pick a rider to deliver this order. Online riders are listed first.';

            // Pre-select the existing rider, if any.
            modal.querySelectorAll('input[name="rider_id"]').forEach(function (input) {
                input.checked = payload.rider_id && Number(input.value) === Number(payload.rider_id);
            });

            modal.removeAttribute('hidden');
            document.body.style.overflow = 'hidden';
        };
        var close = function () {
            modal.setAttribute('hidden', '');
            document.body.style.overflow = '';
        };

        document.querySelectorAll('[data-assign-rider]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                try {
                    open(JSON.parse(btn.getAttribute('data-assign-rider')));
                } catch (e) { /* ignore malformed payload */ }
            });
        });
        modal.querySelectorAll('[data-modal-close]').forEach(function (btn) {
            btn.addEventListener('click', close);
        });
        modal.addEventListener('click', function (e) {
            if (e.target === modal) close();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hasAttribute('hidden')) close();
        });
    })();
</script>
