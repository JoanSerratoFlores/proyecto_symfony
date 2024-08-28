<?php

namespace App\Controller;

use Faker\Provider\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController {

    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine) {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRole('ROLE_USER');

            $date_now = (new \DateTime('now'))->setTimezone(new \DateTimeZone('GMT-6'))->format('y-m-d H:i:s');
            $user->setCreatedAt($date_now);

            //CIFRAR LA CONTRASEÑA
            $passwordHasher = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordHasher);

            // Guardar usuario
            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('tasks');
        }



        return $this->render('user/register.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    public function login(AuthenticationUtils $autenticacionUtils) {

        $error = $autenticacionUtils->getLastAuthenticationError();

        $lastUsername = $autenticacionUtils->getLastUsername();

        return $this->render('user/login.html.twig', array(
        'error' => $error,
        'last_username' => $lastUsername
        ));
    }
}
