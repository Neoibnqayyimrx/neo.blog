<?php
/**
 * @var array $categories
 * @var string|null $success
 * @var string|null $error
 */
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i> <span>Categories</span>
        </div>
        <div class="admin-topbar__title"><h1>Categories</h1></div>
        <div class="admin-topbar__sub">Topics used to group posts on the blog.</div>
    </div>
</div>

<div class="admin-content">
    <?php if ($success): ?>
        <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <div class="admin-grid">
        <div class="card">
            <div class="card__header">
                <div>
                    <h3 class="card__title">All categories</h3>
                    <div class="card__subtitle">Click the pencil to rename, the bin to delete (categories with posts cannot be deleted).</div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Posts</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="4" class="table__empty">No categories yet.</td></tr>
                        <?php else: foreach ($categories as $c): ?>
                            <tr>
                                <td class="table__id">#<?= (int) $c['id'] ?></td>
                                <td class="table__title"><span class="color-dot"></span> <span class="cat-name"><?= e($c['title']) ?></span></td>
                                <td class="table__muted"><?= (int) $c['post_count'] ?></td>
                                <td>
                                    <div class="table__actions">
                                        <button type="button" class="table__action table__action--edit" data-edit-category
                                                data-id="<?= (int) $c['id'] ?>"
                                                data-title="<?= e($c['title']) ?>"
                                                title="Rename">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <form action="<?= url('/admin/categories/delete') ?>" method="post"
                                              onsubmit="return confirm('Delete category &quot;<?= e($c['title']) ?>&quot;? This only works if it has no posts.');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                            <button type="submit"
                                                    class="table__action table__action--danger <?= (int) $c['post_count'] > 0 ? 'is-disabled' : '' ?>"
                                                    <?= (int) $c['post_count'] > 0 ? 'disabled' : '' ?>
                                                    title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card__header">
                <div>
                    <h3 class="card__title" id="formTitle">Add a new category</h3>
                    <div class="card__subtitle">Keep names short and Title-Cased for a clean look.</div>
                </div>
            </div>
            <div class="card__body">
                <form id="categoryForm" action="<?= url('/admin/categories/store') ?>" method="post" class="inline-form">
                    <?= csrfField() ?>
                    <input type="hidden" id="category_id" name="category_id" value="">
                    <input type="text" id="title" name="title" class="form-input"
                           placeholder="e.g. Philosophy of Mind" maxlength="100" required>
                    <button type="submit" class="btn">
                        <i class="ri-add-line"></i> <span id="submitLabel">Create</span>
                    </button>
                    <button type="button" id="cancelEdit" class="btn btn--ghost" hidden>Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const form      = document.getElementById('categoryForm');
    const titleEl   = document.getElementById('title');
    const idEl      = document.getElementById('category_id');
    const formTitle = document.getElementById('formTitle');
    const submitLbl = document.getElementById('submitLabel');
    const cancelBtn = document.getElementById('cancelEdit');

    document.querySelectorAll('[data-edit-category]').forEach(btn => {
        btn.addEventListener('click', () => {
            idEl.value      = btn.dataset.id;
            titleEl.value   = btn.dataset.title;
            form.action     = '<?= url('/admin/categories/update') ?>';
            formTitle.textContent = 'Rename category';
            submitLbl.textContent = 'Save';
            cancelBtn.hidden = false;
            titleEl.focus();
            titleEl.select();
        });
    });

    cancelBtn?.addEventListener('click', () => {
        idEl.value = '';
        titleEl.value = '';
        form.action = '<?= url('/admin/categories/store') ?>';
        formTitle.textContent = 'Add a new category';
        submitLbl.textContent = 'Create';
        cancelBtn.hidden = true;
    });
})();
</script>
