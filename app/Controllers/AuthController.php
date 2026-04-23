<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\User;

final class AuthController
{
    public function signinForm(): void
    {
        if (Auth::isLoggedIn()) redirect(url('/'));

        View::render('auth/signin', [
            'title'   => 'Sign In — ' . APP_NAME,
            'error'   => Auth::getFlash('signin'),
            'success' => Auth::getFlash('signup-success'),
        ]);
    }

    public function signin(): void
    {
        verifyCsrf();

        $identifier = post('username_email');
        $password   = $_POST['password'] ?? '';

        if ($identifier === '' || $password === '') {
            Auth::setFlash('signin', 'Please enter your username/email and password.');
            Auth::setOldInput($_POST);
            redirect(url('/signin'));
        }

        $userModel = new User();
        $user      = $userModel->findByUsernameOrEmail($identifier);

        // Use the same failure path whether or not the user exists to avoid
        // leaking which usernames are registered.
        if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
            Auth::setFlash('signin', 'Invalid credentials. Please try again.');
            Auth::setOldInput($_POST);
            redirect(url('/signin'));
        }

        Auth::login($user);
        redirect((int) $user['is_admin'] === 1 ? url('/admin') : url('/'));
    }

    public function signupForm(): void
    {
        if (Auth::isLoggedIn()) redirect(url('/'));

        View::render('auth/signup', [
            'title' => 'Create account — ' . APP_NAME,
            'error' => Auth::getFlash('signup'),
        ]);
    }

    public function signup(): void
    {
        verifyCsrf();

        $firstname = post('firstname');
        $lastname  = post('lastname');
        $username  = post('username');
        $email     = filter_var(post('email'), FILTER_VALIDATE_EMAIL) ?: '';
        $pw1       = $_POST['createpassword']  ?? '';
        $pw2       = $_POST['confirmpassword'] ?? '';
        $avatar    = $_FILES['avatar'] ?? null;

        $errors = [];
        if ($firstname === '')           $errors[] = 'First name is required.';
        if ($lastname === '')            $errors[] = 'Last name is required.';
        if ($username === '')            $errors[] = 'Username is required.';
        if ($email === '')               $errors[] = 'A valid email address is required.';
        if (strlen($pw1) < 8)            $errors[] = 'Password must be at least 8 characters.';
        if ($pw1 !== $pw2)               $errors[] = 'Passwords do not match.';

        $userModel = new User();
        if (!$errors && $userModel->existsByUsernameOrEmail($username, $email)) {
            $errors[] = 'That username or email is already registered.';
        }

        if ($errors) {
            Auth::setFlash('signup', implode(' ', $errors));
            Auth::setOldInput($_POST);
            redirect(url('/signup'));
        }

        // Avatar is optional during sign-up; if none supplied, use the default.
        $avatarFilename = 'default-avatar.png';
        if (is_array($avatar) && !empty($avatar['name'])) {
            try {
                $avatarFilename = handleImageUpload($avatar, UPLOAD_DIR);
            } catch (\InvalidArgumentException | \RuntimeException $e) {
                Auth::setFlash('signup', $e->getMessage());
                Auth::setOldInput($_POST);
                redirect(url('/signup'));
            }
        }

        $newId = $userModel->create($firstname, $lastname, $username, $email, $pw1, $avatarFilename);
        if (!$newId) {
            Auth::setFlash('signup', 'Registration failed. Please try again.');
            redirect(url('/signup'));
        }

        Auth::setFlash('signup-success', 'Registration successful. Please sign in.');
        redirect(url('/signin'));
    }

    public function logout(): void
    {
        Auth::logout();
        redirect(url('/'));
    }
}
