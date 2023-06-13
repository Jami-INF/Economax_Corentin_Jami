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
    public function __construct(
        protected EmailSender $mailer,
        protected UserRepository $userRepository,
    )
    {
    }

    public function sendReport(Deal $deal): bool
    {
        $admin = $this->userRepository->findAllAdmin();

        foreach ($admin as $user) {
            $email = $this->mailer->createTemplatedEmail(
                null,
                $user->getEmail(),
                'Signalement d\'une annonce',
                'emails/report_deal.html.twig',
                [
                    'deal' => $deal
                ]
            );

            if(!$this->mailer->sendEmail($email)) {
                return false;
            }
        }
        return true;
    }

}