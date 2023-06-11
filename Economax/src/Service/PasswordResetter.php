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
    public function __construct(
        protected EmailSender $mailer,
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface  $passwordHasher
    )
    {
    }

    public function sendPasswordResetEmail(User $user): bool
    {
        $email = $this->mailer->createTemplatedEmail(
            null,
            $user->getEmail(),
            'Réinitialisation de votre mot de passe',
            'emails/reset_password.html.twig',
            [
                'user' => $user
            ]
        );

        return $this->mailer->sendEmail($email);
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