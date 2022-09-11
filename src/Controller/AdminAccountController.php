<?php

namespace App\Controller;

use App\Repository\JobPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdminAccountController extends AbstractController
{
    /**
     * @Route("/muscade", name="admin_index")
     * @IsGranted("ROLE_ADMIN")
     * @param UserRepository $userRepository
     * @param JobPostRepository $jobPostRepository
     * @return Response
     * @throws NonUniqueResultException
     */

    public function index(UserRepository $userRepository, JobPostRepository $jobPostRepository)
    {
        $userCount = $userRepository->countUsers();
        $jobCount = $jobPostRepository->countJobs();

        return $this->render('admin/index.html.twig', [
            'title' => 'dashboard',
            'users_count' => $userCount,
            'jobs_count' => $jobCount,
        ]);
    }
}