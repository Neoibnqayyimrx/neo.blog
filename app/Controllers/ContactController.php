<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;

final class ContactController
{
    public function show(): void
    {
        View::render('pages/contact', [
            'title'      => 'Contact — ' . APP_NAME,
            'activeLink' => 'contact',
            'success'    => Auth::getFlash('contact_success'),
            'error'      => Auth::getFlash('contact_error'),
        ]);
    }

    public function send(): void
    {
        verifyCsrf();

        $name    = post('name');
        $email   = filter_var(post('email'), FILTER_VALIDATE_EMAIL) ?: '';
        $subject = post('subject');
        $message = post('message');

        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            Auth::setFlash('contact_error', 'Please fill in all fields with valid values.');
        } else {
            // In a production build you would send an email here (mail() / PHPMailer).
            Auth::setFlash('contact_success', 'Thanks — your message has been received.');
        }
        redirect(url('/contact'));
    }
}
