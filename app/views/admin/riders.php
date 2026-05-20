<div class="adm-card">
    <h2 class="adm-card-title">Riders (<?= count($riders) ?>)</h2>
    <p class="adm-form-note">
        Riders register themselves from the public site. <strong>Approve</strong>
        to activate them, or <strong>reject</strong> to deny access.
        <?php if (count($pending)): ?>
            <strong><?= count($pending) ?></strong> pending application<?= count($pending) === 1 ? '' : 's' ?>.
        <?php endif; ?>
    </p>
    <?php if (!$riders): ?>
        <p class="adm-empty">No riders yet.</p>
    <?php else: ?>
        <table class="adm-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Contact</th><th>Vehicle</th><th>License</th><th>Status</th><th>Available</th><th>Submitted</th><th>Action</th></tr>
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
                        <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($r['created_at']))) ?></td>
                        <td>
                            <div class="adm-inline-form">
                                <?php if ($r['application_status'] !== 'approved'): ?>
                                    <form action="<?= BASE_URL ?>admin/riders" method="post">
                                        <input type="hidden" name="rider_id" value="<?= (int) $r['id'] ?>">
                                        <input type="hidden" name="application_status" value="approved">
                                        <button type="submit" class="adm-btn-icon adm-btn-approve" title="Approve" aria-label="Approve">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($r['application_status'] !== 'rejected'): ?>
                                    <form action="<?= BASE_URL ?>admin/riders" method="post">
                                        <input type="hidden" name="rider_id" value="<?= (int) $r['id'] ?>">
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
