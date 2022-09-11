<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\AdminUpdatePasswordType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminUserController extends AbstractController
{
    /**
     * @Route("/muscade/user", name="admin_users")
     * @IsGranted("ROLE_ADMIN")
     * @param PaginatorInterface $paginator
     * @param UserRepository $repository
     * @param Request $request
     * @return Response
     */

    public function index(PaginatorInterface $paginator, UserRepository $repository, Request $request)
    {

        // Recupere les annonces grace a l'injection du JobPostRepository
        $users = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/index.html.twig', [
            'title' => 'user',
            'users' => $users
        ]);
    }


    /**
     * @Route("/muscade/user/{id}/edit", name="admin_user_edit")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws
     */

    public function edit(Request $request, ObjectManager $manager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $userRepository->findUserByVal($request->get('id'), 'id');

        $formEmail = $this->createForm(AccountType::class, $user);

        $formEmail->handleRequest($request);

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $manager->flush();

            $this->addFlash(
                'success',
                'Successfully updated ' . $user->getUsername() . ' email'
            );
        }

        $formPassword = $this->createForm(AdminUpdatePasswordType::class, $user);

        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {

            $user->setPassword($passwordEncoder->encodePassword($user, $formPassword->get('newPassword')->getData()));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Successfully modified password"
            );
        }

        return $this->render('admin/user/edit_user.html.twig', [
            'title' => 'edit ' . $user->getUsername(),
            'user' => $user,
            'formEmail' => $formEmail->createView(),
            'formPassword' => $formPassword->createView(),
        ]);
    }

    /**
     * @Route("/muscade/user/{id}/deactivate", name="admin_user_deactivate")
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */

    public function deactivate(User $user, ObjectManager $manager)
    {
        $user->setActive(false);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'succeffully deactivated ' . $user->getUsername() . ' account.');

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/muscade/user/{id}/activate", name="admin_user_activate")
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */

    public function activate(User $user, ObjectManager $manager)
    {
        $user->setActive(true);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'succeffully activated ' . $user->getUsername() . ' account.');

        return $this->redirectToRoute('admin_users');
    }


    /**
     * @Route("/muscade/user/{id}/delete", name="admin_user_delete")
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */

    public function delete(User $user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', 'succeffully deleted ' . $user->getUsername() . ' account.');

        return $this->redirectToRoute('admin_users');
    }
}