<?php
/**
 * @var array $categories
 * @var int|null $activeCategoryId
 */
$activeCategoryId = $activeCategoryId ?? null;
?>
<div class="categories-row">
    <a href="<?= url('/blog') ?>" class="chip <?= $activeCategoryId === null ? 'is-active' : '' ?>">All</a>
    <?php foreach ($categories as $c): ?>
        <a href="<?= url('/category?id=' . (int) $c['id']) ?>"
           class="chip <?= $activeCategoryId === (int) $c['id'] ? 'is-active' : '' ?>">
            <?= e($c['title']) ?>
        </a>
    <?php endforeach; ?>
</div>
