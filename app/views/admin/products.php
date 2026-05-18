<div class="adm-card">
    <h2 class="adm-card-title">Add a product</h2>
    <?php if (!$merchants): ?>
        <p class="adm-empty">Create a restaurant first — products belong to a restaurant.</p>
    <?php else: ?>
        <form action="<?= BASE_URL ?>admin/products" method="post" class="adm-form" enctype="multipart/form-data">
            <div class="adm-form-grid">
                <label>Restaurant
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
                    <select name="category">
                        <option value="">— Select —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>" <?= $old['category'] === $c ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="adm-col-2">Description
                    <input type="text" name="description" value="<?= htmlspecialchars($old['description']) ?>">
                </label>
                <label>Image
                    <span class="adm-upload">
                        <svg class="adm-upload-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                    </span>
                    <small class="adm-hint">JPG, PNG, WEBP or GIF — max 5&nbsp;MB. Optional.</small>
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
