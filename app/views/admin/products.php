<div class="adm-card">
    <h2 class="adm-card-title">All products (<?= count($products) ?>)</h2>
    <p class="adm-form-note">
        Restaurant owners add and manage their own menu items from the
        <strong>merchant portal</strong>. This is a read-only view of every product across all restaurants.
    </p>
    <?php if (!$products): ?>
        <p class="adm-empty">No products yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Restaurant</th><th>Category</th><th>Price</th><th>Available</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= (int) $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['business_name']) ?></td>
                        <td><?= htmlspecialchars($p['category']) ?></td>
                        <td>₱<?= number_format((float) $p['price'], 2) ?></td>
                        <td><?= ((int) $p['is_available'] === 1) ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
