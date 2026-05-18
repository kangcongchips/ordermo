<div class="adm-card">
    <h2 class="adm-card-title">All customers (<?= count($customers) ?>)</h2>
    <?php if (!$customers): ?>
        <p class="adm-empty">No customers yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Address</th><th>Status</th><th>Joined</th></tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?= (int) $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['phone']) ?></td>
                        <td><?= htmlspecialchars($c['city_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($c['default_address'] ?? '—') ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($c['status']) ?>"><?= htmlspecialchars(ucfirst($c['status'])) ?></span></td>
                        <td><?= htmlspecialchars(date('M j, Y', strtotime($c['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
