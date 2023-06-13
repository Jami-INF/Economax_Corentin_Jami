<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegister
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function addAdmin(string $email, string $plainPassword, string $username)
    {
        $admin = new User();

        $admin->setPassword($plainPassword);
        $admin->setEmail($email);
        $admin->setUsername($username);
        $admin->setRoles(['ROLE_ADMIN']);

        $errors = $this->validator->validate($admin);
        if (count($errors) > 0) {
            return $errors;
        }

        $password = $this->passwordHasher->hashPassword(
            $admin,
            $admin->getPassword()
        );

        $admin->setPassword($password);

        $em = $this->doctrine->getManager();
        $em->persist($admin);
        $em->flush();

        return 'Admin created!';
    }
}