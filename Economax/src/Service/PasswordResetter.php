<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetter
{
    private MailerInterface $mailer;
    protected string $NO_REPLY_EMAIL = 'noreply@economax.com';

    public function __construct(
        MailerInterface $mailer,
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface  $passwordHasher
    )
    {
        $this->mailer = $mailer;
    }

    public function sendPasswordResetEmail(User $user): bool
    {
        $email = (new TemplatedEmail())
            ->from($this->NO_REPLY_EMAIL)
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context([
                'user' => $user
            ]);

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
    public function resetPassword(string $email): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // On contrôle si l'utilisateur existe
        if (!$user) {
            return false;
        }

        // Vu qu'il existe, on lui créé un token
        $token = bin2hex(random_bytes(10));
        $user->setToken($token);

        $this->userRepository->save($user);

        return $this->sendPasswordResetEmail($user);
    }

    public function changePassword(string $token, string $pleinTextPassword): bool
    {
        $user = $this->userRepository->findOneBy(['token' => $token]);

        // On contrôle si l'utilisateur existe
        if (!$user) {
            return false;
        }

        // On change son mot de passe
        $password = $this->passwordHasher->hashPassword(
            $user,
            $pleinTextPassword
        );

        $user->setPassword($password);
        $user->setToken(null);

        $this->userRepository->save($user);

        return true;
    }
}