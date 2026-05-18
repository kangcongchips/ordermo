<div class="adm-card">
    <h2 class="adm-card-title">Add a product</h2>
    <?php if (!$merchants): ?>
        <p class="adm-empty">Create a merchant first — products belong to a merchant.</p>
    <?php else: ?>
        <form action="<?= BASE_URL ?>admin/products" method="post" class="adm-form">
            <div class="adm-form-grid">
                <label>Merchant
                    <select name="merchant_id" required>
                        <option value="">— Select —</option>
                        <?php foreach ($merchants as $m): ?>
                            <option value="<?= (int) $m['id'] ?>" <?= (string) $old['merchant_id'] === (string) $m['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['business_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Name
                    <input type="text" name="name" value="<?= htmlspecialchars($old['name']) ?>" required>
                </label>
                <label>Price (₱)
                    <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($old['price']) ?>" required>
                </label>
                <label>Category
                    <input type="text" name="category" value="<?= htmlspecialchars($old['category']) ?>" placeholder="e.g. Drinks">
                </label>
                <label class="adm-col-2">Description
                    <input type="text" name="description" value="<?= htmlspecialchars($old['description']) ?>">
                </label>
                <label>Image filename
                    <input type="text" name="image" value="<?= htmlspecialchars($old['image']) ?>" placeholder="e.g. coffee.jpg">
                </label>
                <label class="adm-check">
                    <input type="checkbox" name="is_available" value="1" <?= $old['is_available'] === '1' ? 'checked' : '' ?>>
                    Available
                </label>
            </div>
            <button type="submit" class="adm-btn">Add product</button>
        </form>
    <?php endif; ?>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">All products (<?= count($products) ?>)</h2>
    <?php if (!$products): ?>
        <p class="adm-empty">No products yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Merchant</th><th>Category</th><th>Price</th><th>Available</th></tr>
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
