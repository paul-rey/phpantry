<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\AccountType;
use App\Form\NewPasswordType;
use App\Form\UpdatePasswordType;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{

    /**
     * @Route("/account", name="account_index")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */

    public function index(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();

        $formEmail = $this->createForm(AccountType::class, $user);

        $formEmail->handleRequest($request);

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                'Successfully updated email'
            );
        }

        $formPassword = $this->createForm(UpdatePasswordType::class, $user);

        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            if (!$passwordEncoder->isPasswordValid($user, $formPassword->get('currentPassword')->getData())) {
                $formPassword->get('currentPassword')->addError(new FormError("Wrong password"));
            } else {
                // encode new password
                $user->setPassword($passwordEncoder->encodePassword($user, $formPassword->get('newPassword')->getData()));

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Successfully modified password"
                );
            }
        }

        return $this->render('account/index.html.twig', [
            'title' => 'Account ' . $this->getUser()->getUsername(),
            'formEmail' => $formEmail->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    /**
     * @Route("/account/my-jobs", name="account_jobs")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function myJobs(PaginatorInterface $paginator, Request $request)
    {
        $jobs = $paginator->paginate(
            $this->getUser()->getJobPosts(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('account/show_jobs.html.twig', [
            'title' => 'account ' . $this->getUser()->getUsername(),
            'user' => $this->getUser(),
            'jobs' => $jobs,
        ]);
    }

    /**
     * @Route("/account/delete", name="account_delete")
     * @Security("is_granted('ROLE_USER') and user == user")
     * @param ObjectManager $manager
     * @return RedirectResponse
     */

    public function delete(ObjectManager $manager)
    {
        $user = $this->getUser();
        $manager->remove($user);
        $manager->flush();

        $session = new Session();
        $session->invalidate();

        return $this->redirectToRoute('home_page');
    }

}
