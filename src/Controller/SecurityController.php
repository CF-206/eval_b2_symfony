<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // #[Route('/add_admin', name: 'app_add_admin')]
    // public function add(UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager) : void {
    //     $user = new User();
    //     $user->setEmail("admin@gmail.com");
    //     $user->setPassword($userPasswordHasher->hashPassword($user, "Azerty@206"));
    //     $user->setRoles(['ROLE_ADMIN']);
    //     $entityManager->persist($user);
    //     $entityManager->flush();
    // }

    // #[Route('/normalUser', name: 'app_normalUser')]
    // public function addnormal(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager) : void {
    //     $user = new User();
    //     $user->setEmail("test@gmail.com");
    //     $user->setPassword($userPasswordHasher->hashPassword($user, "Test_@206"));
    //     $user->setRoles(['ROLE_USER']);
    //     $entityManager->persist($user);
    //     $entityManager->flush();
    // }
}
