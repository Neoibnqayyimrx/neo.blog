<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\User;

final class UserController
{
    public function index(): void
    {
        Auth::requireAdmin();

        View::render('admin/users/index', [
            'title'      => 'Users — Admin',
            'activeLink' => 'users',
            'users'      => (new User())->getAll(),
            'success'    => Auth::getFlash('admin-success'),
            'error'      => Auth::getFlash('admin-error'),
        ], layout: 'admin');
    }

    public function edit(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) redirect(url('/admin/users'));

        $user = (new User())->getById($id);
        if (!$user) {
            Auth::setFlash('admin-error', 'User not found.');
            redirect(url('/admin/users'));
        }

        View::render('admin/users/edit', [
            'title'      => 'Edit user — Admin',
            'activeLink' => 'users',
            'user'       => $user,
            'success'    => Auth::getFlash('admin-success'),
            'error'      => Auth::getFlash('admin-error'),
        ], layout: 'admin');
    }

    public function update(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $id          = (int) post('user_id');
        $firstname   = post('firstname');
        $lastname    = post('lastname');
        $username    = post('username');
        $email       = filter_var(post('email'), FILTER_VALIDATE_EMAIL) ?: '';
        $newPassword = $_POST['new_password'] ?? '';
        $avatarFile  = $_FILES['avatar'] ?? null;

        if ($id <= 0) redirect(url('/admin/users'));
        $back = url('/admin/users/edit') . '?id=' . $id;

        $errors = [];
        if ($firstname === '') $errors[] = 'First name is required.';
        if ($lastname === '')  $errors[] = 'Last name is required.';
        if ($username === '')  $errors[] = 'Username is required.';
        if ($email === '')     $errors[] = 'A valid email is required.';
        if ($newPassword !== '' && strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }
        if ($errors) {
            Auth::setFlash('admin-error', implode(' ', $errors));
            redirect($back);
        }

        $userModel = new User();
        $existing  = $userModel->getById($id);
        if (!$existing) {
            Auth::setFlash('admin-error', 'User not found.');
            redirect(url('/admin/users'));
        }

        if ($userModel->usernameTaken($username, $id)) {
            Auth::setFlash('admin-error', 'That username is already taken.');
            redirect($back);
        }
        if ($userModel->emailTaken($email, $id)) {
            Auth::setFlash('admin-error', 'That email is already registered.');
            redirect($back);
        }

        $avatarFilename = $existing['avatar'];
        if (is_array($avatarFile) && !empty($avatarFile['name'])) {
            try {
                $newAvatar = handleImageUpload($avatarFile, UPLOAD_DIR);
                if ($existing['avatar'] !== 'default-avatar.png') {
                    $old = UPLOAD_DIR . $existing['avatar'];
                    if (is_file($old)) @unlink($old);
                }
                $avatarFilename = $newAvatar;
            } catch (\InvalidArgumentException | \RuntimeException $e) {
                Auth::setFlash('admin-error', $e->getMessage());
                redirect($back);
            }
        }

        $userModel->updateProfile($id, $firstname, $lastname, $username, $email, $avatarFilename);
        if ($newPassword !== '') {
            $userModel->updatePassword($id, $newPassword);
        }

        // Rotate CSRF after a successful state change for a little extra safety.
        unset($_SESSION['csrf_token']);
        Auth::setFlash('admin-success', 'User updated successfully.');
        redirect(url('/admin/users'));
    }

    public function toggleAdmin(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $id = (int) post('id');
        if ($id <= 0) redirect(url('/admin/users'));

        if ($id === Auth::userId()) {
            Auth::setFlash('admin-error', 'You cannot change your own admin status.');
            redirect(url('/admin/users'));
        }

        $userModel = new User();
        $user      = $userModel->getById($id);
        if (!$user) {
            Auth::setFlash('admin-error', 'User not found.');
            redirect(url('/admin/users'));
        }

        $newStatus = ((int) $user['is_admin']) !== 1;
        $userModel->setAdmin($id, $newStatus);

        $name = $user['firstname'] . ' ' . $user['lastname'];
        Auth::setFlash('admin-success', $newStatus
            ? '"' . $name . '" has been granted admin rights.'
            : '"' . $name . '" has had admin rights revoked.');
        redirect(url('/admin/users'));
    }

    public function delete(): void
    {
        Auth::requireAdmin();
        verifyCsrf();

        $id = (int) post('id');
        if ($id <= 0) redirect(url('/admin/users'));

        if ($id === Auth::userId()) {
            Auth::setFlash('admin-error', 'You cannot delete your own account.');
            redirect(url('/admin/users'));
        }

        $userModel = new User();
        $user      = $userModel->getById($id);
        if (!$user) {
            Auth::setFlash('admin-error', 'User not found.');
            redirect(url('/admin/users'));
        }

        if (!empty($user['avatar']) && $user['avatar'] !== 'default-avatar.png') {
            $path = UPLOAD_DIR . $user['avatar'];
            if (is_file($path)) @unlink($path);
        }

        $userModel->delete($id);
        Auth::setFlash('admin-success', '"' . $user['firstname'] . ' ' . $user['lastname'] . '" has been deleted.');
        redirect(url('/admin/users'));
    }
}
