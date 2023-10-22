<?php

namespace App\Controller;

use LogicException;
use App\Entity\User;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passEncoder){
        $entityManager = $this->getDoctrine()->getManager();
                
        $form = $this->createFormBuilder()
                ->add('username', TextType::class, [
                    'label' => 'Utilisateur',
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation du mot de passe'],
                ])
                ->add('email', TextType::class, [
                    'label' => 'E-mail',
                ])
                ->add('adresse', TextareaType::class, [
                    'label' => 'Adresse',
                ])

                ->add('roles', ChoiceType::class, [
                    'label' => 'Privilèges',
                    'choices' => [
                        'ROLE_USER' => 'ROLE_USER',
                        'ROLE_ADMIN' => 'ROLE_ADMIN',
                        'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                    ],
                    'multiple' => true,
                ])

                ->add('valider', SubmitType::class, [
                    'label' => 'Valider',
                    'attr' => [
                        'style' => 'margin-top: 20px;',
                        ]
                ])
                ->getForm();
                ;
        
        $form->handleRequest($request);
        
        if($request->isMethod('post') && $form->isValid()){

            $data = $form->getData();
            $user = new \App\Entity\Client;
            $user->setUsername($data['username']);
            $user->setPassword($passEncoder->encodePassword($user, $data['password']));
            if(isset($data['roles'])){ // Nous vérifions si les roles ont été activés ou sont mis en commentaire
                $user->setRoles($data['roles']);
            } else $user->setRoles(['ROLE_USER']);
            $user->setAdresse($data['adresse']);
            $user->setEmail($data['email']);
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirect($this->generateUrl('index'));
        }
        
        return $this->render('index/form.html.twig', [
            'produitForm' => $form->createView(),
        ]);
        
    }
    
}
