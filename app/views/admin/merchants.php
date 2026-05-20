<div class="adm-card">
    <h2 class="adm-card-title">Restaurants (<?= count($merchants) ?>)</h2>
    <p class="adm-form-note">
        Restaurant owners register themselves from the public site.
        <strong>Approve</strong> to list the restaurant publicly, or
        <strong>reject</strong> to deny access.
        <?php if (count($pending)): ?>
            <strong><?= count($pending) ?></strong> pending application<?= count($pending) === 1 ? '' : 's' ?>.
        <?php endif; ?>
    </p>
    <?php if (!$merchants): ?>
        <p class="adm-empty">No restaurants yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Restaurant</th><th>Owner</th><th>Contact</th><th>City</th><th>Cuisine</th><th>Status</th><th>Open</th><th>Submitted</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($merchants as $m): ?>
                    <tr>
                        <td><?= (int) $m['id'] ?></td>
                        <td><?= htmlspecialchars($m['business_name']) ?><br><span class="adm-muted"><?= htmlspecialchars($m['business_address']) ?></span></td>
                        <td><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?></td>
                        <td><?= htmlspecialchars($m['phone']) ?><br><span class="adm-muted"><?= htmlspecialchars($m['email']) ?></span></td>
                        <td><?= htmlspecialchars($m['city_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($m['cuisine'] !== '' ? $m['cuisine'] : '—') ?></td>
                        <td><span class="adm-badge adm-badge-<?= htmlspecialchars($m['application_status']) ?>"><?= htmlspecialchars(ucfirst($m['application_status'])) ?></span></td>
                        <td><?= ((int) $m['is_open'] === 1) ? 'Yes' : 'No' ?></td>
                        <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($m['created_at']))) ?></td>
                        <td>
                            <div class="adm-inline-form">
                                <?php if ($m['application_status'] !== 'approved'): ?>
                                    <form action="<?= BASE_URL ?>admin/merchants" method="post">
                                        <input type="hidden" name="merchant_id" value="<?= (int) $m['id'] ?>">
                                        <input type="hidden" name="application_status" value="approved">
                                        <button type="submit" class="adm-btn-icon adm-btn-approve" title="Approve" aria-label="Approve">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($m['application_status'] !== 'rejected'): ?>
                                    <form action="<?= BASE_URL ?>admin/merchants" method="post">
                                        <input type="hidden" name="merchant_id" value="<?= (int) $m['id'] ?>">
                                        <input type="hidden" name="application_status" value="rejected">
                                        <button type="submit" class="adm-btn-icon adm-btn-reject" title="Reject" aria-label="Reject">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
