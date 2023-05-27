<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Admin\User\UserPasswordType;
use App\Form\SignUpType;
use App\Repository\UserRepository;
use App\Service\PasswordResetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Config\Security\PasswordHasherConfig;


class LoginController extends AbstractController
{

    public function __construct(
        protected UserRepository $userRepository,
        protected PasswordResetter $passwordResetter
    )
    {
    }
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
              'last_username' => $lastUsername,
              'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $this->userRepository->save($user);

            return $this->redirectToRoute("app_home");
        }
        return $this->render('login/signup.html.twig', [
            "form" => $form,
        ]);
    }

    #[Route(path: '/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->get('_csrf_token');
            if ($this->isCsrfTokenValid('reset-password', $token)) {
                $emailAddress = $request->get('_username');
                $this->passwordResetter->resetPassword($emailAddress);
                // Peu importe si l'email existe ou non, on envoie ce message pour ne pas donner d'info à l'user
                $this->addFlash('success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('login/reset_password.html.twig');
    }

    #[Route('/change-password/{token}', name: 'app_change_password')]
    public function changePassword(User $user, Request $request): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->passwordResetter->changePassword($user->getToken(), $user->getPassword());
            $this->addFlash('success', 'Votre mot de passe a bien été modifié.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
