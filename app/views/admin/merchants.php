<div class="adm-card">
    <h2 class="adm-card-title">Add a restaurant</h2>
    <p class="adm-form-note">
        This creates an <strong>owner login</strong> (so they can sign in and manage the store)
        <strong>and</strong> the <strong>restaurant</strong> customers see in the app — in one step.
    </p>
    <form action="<?= BASE_URL ?>admin/merchants" method="post" class="adm-form" enctype="multipart/form-data">

        <div class="adm-form-section">
            <h3 class="adm-form-section-title">Owner account</h3>
            <p class="adm-form-section-sub">The person who logs in to manage this restaurant.</p>
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
            </div>
        </div>

        <div class="adm-form-section">
            <h3 class="adm-form-section-title">Restaurant details</h3>
            <p class="adm-form-section-sub">What customers see when browsing the app.</p>
            <div class="adm-form-grid">
                <label>Restaurant name
                    <input type="text" name="business_name" value="<?= htmlspecialchars($old['business_name']) ?>" required>
                </label>
                <label class="adm-col-2">Address
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
                <label>Photo
                    <span class="adm-upload">
                        <svg class="adm-upload-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp,image/gif">
                    </span>
                    <small class="adm-hint">JPG, PNG, WEBP or GIF — max 5&nbsp;MB. Optional.</small>
                </label>
                <label>Listing status
                    <select name="application_status">
                        <?php foreach (['approved', 'pending', 'rejected'] as $s): ?>
                            <option value="<?= $s ?>" <?= $old['application_status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </div>

        <button type="submit" class="adm-btn">Create restaurant</button>
    </form>
</div>

<div class="adm-card">
    <h2 class="adm-card-title">All restaurants (<?= count($merchants) ?>)</h2>
    <?php if (!$merchants): ?>
        <p class="adm-empty">No restaurants yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Restaurant</th><th>Owner</th><th>City</th><th>Cuisine</th><th>Status</th><th>Open</th></tr>
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
