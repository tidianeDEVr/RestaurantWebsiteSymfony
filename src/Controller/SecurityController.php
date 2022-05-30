<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Client;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();
            if($roles[0]=='ROLE_GESTIONNAIRE'){
                return $this->redirectToRoute('dashboard');
            }else{
                return $this->redirectToRoute('home');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $client = new Client();
        $form = $this->createFormBuilder($client)
        ->add('nomComplet', TextType::class, [
            'attr' => [
                'placeholder' => 'Entrez votre nom complet',
                'class' => 'form-control'
            ]
        ])
        ->add('login', TextType::class, [
            'attr' => [
                'placeholder' => 'Entre votre adresse email',
                'class' => 'form-control'
            ]
        ])
        ->add('password', PasswordType::class, [
            'attr' => [
                'placeholder' => 'Entre votre adresse domicile',
                'class' => 'form-control'
            ]
        ])
        ->add('domicile', TextType::class, [
            'attr' => [
                'placeholder' => 'Entre votre adresse domicile',
                'class' => 'form-control'
            ]
        ])
        ->add('telephone', NumberType::class, [
            'attr' => [
                'placeholder' => 'Entre votre numero de telephone',
                'class' => 'form-control'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $passwordEncode= $encoder->hashPassword($client,$client->getPassword());
            $client->setPassword($passwordEncode);
            $manager->persist($client);
            $manager->flush();
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'controller_name' => 'SecurityController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
