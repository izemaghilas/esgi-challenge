<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $from
    ) {
    }

    public function sendEmailConfirmationMail(string $signedUrl, string $to, string $firstname)
    {
        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to($to)
            ->subject('Confirmer votre compte')
            ->htmlTemplate('confirm-email.html.twig')
            ->context([
                'firstname' => $firstname,
                'confirmationUrl' => $signedUrl
            ]);

        $this->mailer->send($email);
    }
}