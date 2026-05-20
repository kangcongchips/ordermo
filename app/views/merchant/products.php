<div class="mdash-page-head">
    <div>
        <h2 class="mdash-page-title">Products <span class="mdash-page-count">(<?= count($products) ?>)</span></h2>
        <p class="mdash-page-sub">Build the menu customers will order from. Hidden items stay here but don't show publicly.</p>
    </div>
    <button type="button" class="mdash-cta" data-modal-open="add-product">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add product
    </button>
</div>

<div class="adm-card">
    <?php if (!$products): ?>
        <div class="mdash-empty">
            <span class="mdash-empty-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16v16H4z"/>
                    <path d="M8 8h8M8 12h8M8 16h5"/>
                </svg>
            </span>
            <p class="mdash-empty-title">Your menu is empty</p>
            <p class="mdash-empty-sub">Add your first item — name, price, and a tasty photo go a long way.</p>
            <button type="button" class="adm-btn" data-modal-open="add-product" style="margin-top:0.85rem;">
                + Add your first product
            </button>
        </div>
    <?php else: ?>
        <div class="mdash-product-grid">
            <?php foreach ($products as $p): ?>
                <article class="mdash-product <?= (int) $p['is_available'] === 1 ? '' : 'is-hidden' ?>">
                    <div class="mdash-product-image"
                        <?php if (!empty($p['image'])): ?>
                            style="background-image:url('<?= BASE_URL ?>images/food/<?= htmlspecialchars($p['image']) ?>')"
                        <?php endif; ?>>
                        <?php if (empty($p['image'])): ?>
                            <span class="mdash-product-placeholder" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <path d="M21 15l-5-5L5 21"/>
                                </svg>
                            </span>
                        <?php endif; ?>
                        <span class="mdash-product-badge adm-badge adm-badge-<?= (int) $p['is_available'] === 1 ? 'active' : 'cancelled' ?>">
                            <?= (int) $p['is_available'] === 1 ? 'Available' : 'Hidden' ?>
                        </span>
                    </div>
                    <div class="mdash-product-body">
                        <span class="mdash-product-cat"><?= htmlspecialchars($p['category']) ?></span>
                        <h3 class="mdash-product-name"><?= htmlspecialchars($p['name']) ?></h3>
                        <?php if (!empty($p['description'])): ?>
                            <p class="mdash-product-desc"><?= htmlspecialchars($p['description']) ?></p>
                        <?php endif; ?>
                        <span class="mdash-product-price">₱<?= number_format((float) $p['price'], 2) ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="mdash-modal-overlay" data-modal="add-product" role="dialog" aria-modal="true" aria-labelledby="add-product-title" hidden>
    <form action="<?= BASE_URL ?>merchant/products" method="post" class="mdash-modal mdash-modal-wide" enctype="multipart/form-data">
        <button type="button" class="mdash-modal-close" data-modal-close aria-label="Close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <header class="mdash-modal-head">
            <span class="mdash-modal-step">Menu</span>
            <h2 id="add-product-title" class="mdash-modal-title">Add a product</h2>
            <p class="mdash-modal-sub">Fill in the basics — you can always edit details later.</p>
        </header>

        <div class="mdash-modal-body">
            <label class="mdash-field">
                <span class="mdash-field-label">Product name</span>
                <input type="text" name="name" placeholder="e.g. Chicken Inasal" required>
            </label>

            <div class="mdash-field-row">
                <label class="mdash-field">
                    <span class="mdash-field-label">Price (₱)</span>
                    <input type="number" name="price" step="0.01" min="0" placeholder="0.00" required>
                </label>
                <label class="mdash-field">
                    <span class="mdash-field-label">Category</span>
                    <select name="category">
                        <option value="">— Select —</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <label class="mdash-field">
                <span class="mdash-field-label">Description <span class="mdash-field-optional">Optional</span></span>
                <input type="text" name="description" placeholder="A short, mouthwatering line customers will see first">
            </label>

            <label class="mdash-field">
                <span class="mdash-field-label">Photo <span class="mdash-field-optional">Optional</span></span>
                <span class="mdash-upload">
                    <span class="mdash-upload-ico" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    </span>
                    <span class="mdash-upload-text">
                        <strong>Click to upload</strong>
                        <small>JPG, PNG, WEBP or GIF — up to 5&nbsp;MB</small>
                    </span>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                </span>
            </label>

            <label class="mdash-availability">
                <input type="checkbox" name="is_available" value="1" checked>
                <span class="mdash-availability-body">
                    <span class="mdash-availability-title">Available to order</span>
                    <span class="mdash-availability-sub">Customers can see and order this item right away.</span>
                </span>
                <span class="mdash-availability-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </span>
            </label>
        </div>

        <footer class="mdash-modal-foot">
            <button type="button" class="mdash-modal-cancel" data-modal-close>Cancel</button>
            <button type="submit" class="adm-btn">Add product</button>
        </footer>
    </form>
</div>

<script>
    (function () {
        var modal = document.querySelector('[data-modal="add-product"]');
        if (!modal) return;

        var open  = function () { modal.removeAttribute('hidden'); document.body.style.overflow = 'hidden'; };
        var close = function () { modal.setAttribute('hidden', ''); document.body.style.overflow = ''; };

        document.querySelectorAll('[data-modal-open="add-product"]').forEach(function (btn) {
            btn.addEventListener('click', open);
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

        // Auto-open when arriving via ?add=1 (e.g. Dashboard "+ Add product" CTA).
        if (new URLSearchParams(window.location.search).get('add') === '1') {
            open();
        }
    })();
</script>
