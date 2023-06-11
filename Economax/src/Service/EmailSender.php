<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSender
{
    private MailerInterface $mailer;
    protected string $NO_REPLY_EMAIL = 'noreply@economax.com';

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function createTemplatedEmail(
        ?string $from,
        string|array $to,
        string $subject,
        string $template,
        array $context = [],
        string|array|null $cc = null,
        string|array|null $bcc = null,
        ?string $replyTo = null
    ): TemplatedEmail
    {
        (is_string($to)) ? $to = [$to] : $to;
        (is_string($cc)) ? $cc = [$cc] : $cc;
        (is_string($bcc)) ? $bcc = [$bcc] : $bcc;

        $email = (new TemplatedEmail())
            ->from(!empty($from) ? $from : $this->NO_REPLY_EMAIL)
            ->to(...$to);
        if ($cc) {
            $email->cc(...$cc);
        }
        if ($bcc) {
            $email->bcc(...$bcc);
        }
        if ($replyTo) {
            $email->replyTo($replyTo);
        }
        $email->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        return $email;
    }

    public function sendEmail(Email $email): bool
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return false;
        }

        return true;
    }
}