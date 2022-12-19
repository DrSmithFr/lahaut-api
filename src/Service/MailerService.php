<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Twig\Environment;

class MailerService
{
    const EMAIL_FROM = 'noreply@lahaut.fr';
    private Environment $twigRendererEngine;
    private Swift_Mailer $mailer;

    public function __construct(
        Environment $twigRendererEngine,
    )
    {
        $this->twigRendererEngine = $twigRendererEngine;

        $transport = (new Swift_SmtpTransport('smtp.example.org', 25))
            ->setUsername('your username')
            ->setPassword('your password');

        $this->mailer = new Swift_Mailer($transport);
    }

    public function sendResetPasswordMail(User $user): void
    {
        $message = (new Swift_Message('Reset your password'))
            ->setFrom(self::EMAIL_FROM)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twigRendererEngine->render(
                    'emails/password_reset.html.twig',
                    ['user' => $user]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
