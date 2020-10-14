<?php

declare(strict_types=1);

namespace App\Service;

use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

/**
 * Class Mailer
 */
class Mailer
{
    private string $senderEmail;

    private Swift_Mailer $mailer;

    private Environment $twig;

    /**
     * @param Environment  $twig
     * @param Swift_Mailer $mailer
     * @param string       $senderEmail
     */
    public function __construct(Environment $twig, Swift_Mailer $mailer, string $senderEmail)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->twig = $twig;
    }

    /**
     * @param string      $userEmail
     * @param string      $subject
     * @param string|null $htmlMessage
     * @param string|null $txtMessage
     */
    public function send(string $userEmail, string $subject, string $htmlMessage, ?string $txtMessage = null): void
    {
        $message = (new Swift_Message($subject))
            ->setFrom($this->senderEmail)
            ->setSubject($subject)
            ->setTo($userEmail)
            ->setBody($htmlMessage, 'text/html');

        if ($txtMessage !== null) {
            $message = $message->addPart($txtMessage, 'text/plain');
        }

        $this->mailer->send($message);
    }
}
