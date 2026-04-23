<?php
/**
 * @var array $users
 * @var string|null $success
 * @var string|null $error
 */

use App\Core\Auth;

$currentId = Auth::userId();
?>
<div class="admin-topbar">
    <div>
        <div class="admin-topbar__crumb">
            <a href="<?= url('/admin') ?>">Admin</a> <i class="ri-arrow-right-s-line"></i> <span>Users</span>
        </div>
        <div class="admin-topbar__title"><h1>Users</h1></div>
        <div class="admin-topbar__sub">Manage accounts, grant admin rights, or remove members.</div>
    </div>
</div>

<div class="admin-content">
    <?php if ($success): ?>
        <div class="alert alert--success"><i class="ri-checkbox-circle-line"></i><div><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert--error"><i class="ri-error-warning-line"></i><div><?= e($error) ?></div></div>
    <?php endif; ?>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="5" class="table__empty">No users yet.</td></tr>
                    <?php else: foreach ($users as $user): $uid = (int) $user['id']; $isSelf = $uid === $currentId; ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <span class="avatar avatar--sm">
                                        <img src="<?= avatar_url($user['avatar']) ?>" alt="">
                                    </span>
                                    <div class="user-cell__meta">
                                        <div class="user-cell__name">
                                            <?= e($user['firstname'] . ' ' . $user['lastname']) ?>
                                            <?php if ($isSelf): ?><span class="badge badge--info">You</span><?php endif; ?>
                                        </div>
                                        <div class="user-cell__handle">@<?= e($user['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="table__muted"><?= e($user['email']) ?></td>
                            <td class="table__muted"><?= e(formatDate($user['created_at'], 'M d, Y')) ?></td>
                            <td>
                                <?php if ((int) $user['is_admin'] === 1): ?>
                                    <span class="badge badge--primary badge--dot">Administrator</span>
                                <?php else: ?>
                                    <span class="badge">Member</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table__actions">
                                    <a href="<?= url('/admin/users/edit?id=' . $uid) ?>"
                                       class="table__action table__action--edit" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <?php if (!$isSelf): ?>
                                        <form action="<?= url('/admin/users/toggle-admin') ?>" method="post"
                                              onsubmit="return confirm('<?= (int) $user['is_admin'] === 1 ? 'Remove admin rights?' : 'Grant admin rights to this user?' ?>');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $uid ?>">
                                            <button type="submit" class="table__action"
                                                    title="<?= (int) $user['is_admin'] === 1 ? 'Revoke admin' : 'Make admin' ?>">
                                                <i class="ri-shield-<?= (int) $user['is_admin'] === 1 ? 'cross' : 'star' ?>-line"></i>
                                            </button>
                                        </form>
                                        <form action="<?= url('/admin/users/delete') ?>" method="post"
                                              onsubmit="return confirm('Delete this user? This will also remove all their posts.');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $uid ?>">
                                            <button type="submit" class="table__action table__action--danger" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="table__action is-disabled" title="You cannot modify your own role">
                                            <i class="ri-lock-2-line"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
