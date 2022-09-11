<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RecoverPasswordType;
use App\Form\RegistrationType;
use App\Notification\EmailNotification;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="user_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'title' => 'login',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
    }

    /**
     * @Route("/register", name="user_registration")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param EmailNotification $notification
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, EmailNotification $notification)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPass = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashedPass);

            $manager->persist($user);
            $manager->flush();

            // send a confirmation email to the user email address

            $notification->notifyRegistration($user);

            $emailMessage = 'A confirmation email was sent to %s ,please activate your account before login';
            $emailFormatted = sprintf($emailMessage, $user->getEmail());

            $this->addFlash(
                'success',
                $emailFormatted
            );

//            commenté pour test
            return $this->redirectToRoute('user_login');
        }

        return $this->render('security/register.html.twig', [
            'title' => 'registration',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password-recovery", name="password_recovery")
     * @param Request $request
     * @param ObjectManager $manager
     * @param EmailNotification $notification
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function recoverPassword(Request $request, ObjectManager $manager, EmailNotification $notification)
    {
        $form = $this->createForm(RecoverPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->getData()['email'];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            if ($user) {
                $token = bin2hex(random_bytes(10));

                $user->setToken($token);

                $manager->persist($user);
                $manager->flush();

                $notification->notifyPasswordRecovery($user);

                $this->addFlash(
                    'success',
                    'A recovery link was sent to: ' . $email
                );

            } else {
                $this->addFlash(
                    'success',
                    'No user found for the email address: ' . $email
                );
            }
        }
        return $this->render('security/password_recovery.html.twig', [
            'title' => 'password recovery',
            'form' => $form->createView()
        ]);
    }
}
