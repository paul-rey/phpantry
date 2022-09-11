<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JobPostRepository;


class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home_page")
     * @param JobPostRepository $repository
     * @return mixed
     */
    public function home(JobPostRepository $repository)
    {
        // Recupere les annonces grace a l'injection du JobPostRepository

        $jobs = $repository->findLatestJobCard();

        return $this->render('pages/home.html.twig', [
            'title' => 'home',
            'jobs' => $jobs
        ]);
    }
}