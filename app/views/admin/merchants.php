<div class="adm-card">
    <h2 class="adm-card-title">Add a merchant</h2>
    <form action="<?= BASE_URL ?>admin/merchants" method="post" class="adm-form">
        <div class="adm-form-grid">
            <label>Owner first name
                <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name']) ?>" required>
            </label>
            <label>Owner last name
                <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name']) ?>" required>
            </label>
            <label>Owner email
                <input type="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>
            </label>
            <label>Owner phone
                <input type="tel" name="phone" value="<?= htmlspecialchars($old['phone']) ?>" placeholder="09171234567" required>
            </label>
            <label>Password
                <input type="text" name="password" placeholder="min. 8 characters" required>
            </label>
            <label>Business name
                <input type="text" name="business_name" value="<?= htmlspecialchars($old['business_name']) ?>" required>
            </label>
            <label class="adm-col-2">Business address
                <input type="text" name="business_address" value="<?= htmlspecialchars($old['business_address']) ?>" required>
            </label>
            <label>City
                <select name="city_id">
                    <option value="">— None —</option>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (string) $old['city_id'] === (string) $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name'] . ', ' . $c['province']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Cuisine
                <input type="text" name="cuisine" value="<?= htmlspecialchars($old['cuisine']) ?>" placeholder="e.g. Filipino, Pasta">
            </label>
            <label>Application status
                <select name="application_status">
                    <?php foreach (['approved', 'pending', 'rejected'] as $s): ?>
                        <option value="<?= $s ?>" <?= $old['application_status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <button type="submit" class="adm-btn">Create merchant</button>
    </form>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">All merchants (<?= count($merchants) ?>)</h2>
    <?php if (!$merchants): ?>
        <p class="adm-empty">No merchants yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Business</th><th>Owner</th><th>City</th><th>Cuisine</th><th>Status</th><th>Open</th></tr>
            </thead>
            <tbody>
                <?php foreach ($merchants as $m): ?>
                    <tr>
                        <td><?= (int) $m['id'] ?></td>
                        <td><?= htmlspecialchars($m['business_name']) ?><br><span class="adm-muted"><?= htmlspecialchars($m['business_address']) ?></span></td>
                        <td><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?><br><span class="adm-muted"><?= htmlspecialchars($m['email']) ?></span></td>
                        <td><?= htmlspecialchars($m['city_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($m['cuisine'] !== '' ? $m['cuisine'] : '—') ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($m['application_status']) ?>"><?= htmlspecialchars(ucfirst($m['application_status'])) ?></span></td>
                        <td><?= ((int) $m['is_open'] === 1) ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
