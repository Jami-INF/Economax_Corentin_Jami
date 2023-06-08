<?php

namespace App\Service;

use App\Entity\Deal;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ReportDealMailer
{
    private MailerInterface $mailer;
    protected string $NO_REPLY_EMAIL = 'noreply@economax.com';

    public function __construct(
        MailerInterface $mailer,
        protected UserRepository $userRepository,
    )
    {
        $this->mailer = $mailer;
    }

    public function sendReport(Deal $deal): bool
    {
        $admin = $this->userRepository->findAllAdmin();

        foreach ($admin as $user) {
            $email = (new TemplatedEmail())
                ->from($this->NO_REPLY_EMAIL)
                ->to($user->getEmail())
                ->subject('Signalement d\'une annonce')
                ->htmlTemplate('emails/report_deal.html.twig')
                ->context([
                    'deal' => $deal
                ]);

            try {
                $this->mailer->send($email);

            } catch (TransportExceptionInterface $e) {
                return false;
            }
        }
        return true;
    }

}