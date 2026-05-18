<div class="adm-card">
    <h2 class="adm-card-title">Add a rider</h2>
    <form action="<?= BASE_URL ?>admin/riders" method="post" class="adm-form">
        <div class="adm-form-grid">
            <label>First name
                <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name']) ?>" required>
            </label>
            <label>Last name
                <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name']) ?>" required>
            </label>
            <label>Email
                <input type="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>
            </label>
            <label>Phone
                <input type="tel" name="phone" value="<?= htmlspecialchars($old['phone']) ?>" placeholder="09171234567" required>
            </label>
            <label>Password
                <input type="text" name="password" placeholder="min. 8 characters" required>
            </label>
            <label>Vehicle type
                <select name="vehicle_type">
                    <?php foreach (['motorcycle', 'bicycle', 'car'] as $v): ?>
                        <option value="<?= $v ?>" <?= $old['vehicle_type'] === $v ? 'selected' : '' ?>><?= ucfirst($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>License number
                <input type="text" name="license_number" value="<?= htmlspecialchars($old['license_number']) ?>" required>
            </label>
            <label>Application status
                <select name="application_status">
                    <?php foreach (['approved', 'pending', 'rejected'] as $s): ?>
                        <option value="<?= $s ?>" <?= $old['application_status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <button type="submit" class="adm-btn">Create rider</button>
    </form>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">All riders (<?= count($riders) ?>)</h2>
    <?php if (!$riders): ?>
        <p class="adm-empty">No riders yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Contact</th><th>Vehicle</th><th>License</th><th>Status</th><th>Available</th></tr>
            </thead>
            <tbody>
                <?php foreach ($riders as $r): ?>
                    <tr>
                        <td><?= (int) $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                        <td><?= htmlspecialchars($r['phone']) ?><br><span class="adm-muted"><?= htmlspecialchars($r['email']) ?></span></td>
                        <td><?= htmlspecialchars(ucfirst($r['vehicle_type'])) ?></td>
                        <td><?= htmlspecialchars($r['license_number']) ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($r['application_status']) ?>"><?= htmlspecialchars(ucfirst($r['application_status'])) ?></span></td>
                        <td><?= ((int) $r['is_available'] === 1) ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
