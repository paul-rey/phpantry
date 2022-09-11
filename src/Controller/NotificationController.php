<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\NewPasswordType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NotificationController extends AbstractController
{

    /**
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     * @Route("/verify/{token}", name="verification_email")
     * @throws
     */

    public function confirmRegistration(Request $request, ObjectManager $manager)
    {

        $token = $request->get('token');

        $repository = $manager->getRepository(User::class);
        $user = $repository->findUserByVal($token, 'token');

        if (!$user) {

            $this->addFlash(
                'error',
                'token not found'
            );

            return $this->redirectToRoute('user_login');
        }

        $user->setToken(null);
        $user->setActive(true);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'Successfully verified you can now login'
        );

        return $this->redirectToRoute('user_login');
    }

    /**
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws
     * @Route("/password-recovery/{token}", name="password_email")
     */

    public function confirmPasswordRecovery(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {

        $token = $request->get('token');

        $repository = $manager->getRepository(User::class);
        $user = $repository->findUserByVal($token, 'token');

        if (!$user) {

            $this->addFlash(
                'error',
                'token not found'
            );

            return $this->redirectToRoute('user_login');
        }

        $this->addFlash(
            'success',
            'You can now safely change your password'
        );

        $formPassword = $this->createForm(NewPasswordType::class, $user);

        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {

            // encode new password
            $user->setPassword($passwordEncoder->encodePassword($user, $formPassword->get('newPassword')->getData()));
            $user->setToken(null);

            $manager->persist($user);
            $manager->flush();

            $this->get('session')->getFlashBag()->clear();

            $this->addFlash(
                'success',
                "Your password was successfully modified!"
            );

            return $this->redirectToRoute('user_login');
        }

        return $this->render('security/new_password.html.twig', [
            'title' => 'New password',
            'formPassword' => $formPassword->createView(),
        ]);
    }

}
