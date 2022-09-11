<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Form\JobType;
use App\Repository\JobPostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdminJobController extends AbstractController
{
    /**
     * @Route("/muscade/job", name="admin_jobs")
     * @IsGranted("ROLE_ADMIN")
     * @param PaginatorInterface $paginator
     * @param JobPostRepository $repository
     * @param Request $request
     * @return Response
     */

    public function index(PaginatorInterface $paginator, JobPostRepository $repository, Request $request)
    {

        // Recupere les annonces grace a l'injection du JobPostRepository
        $jobs = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('admin/job/index.html.twig', [
            'title' => 'job',
            'jobs' => $jobs
        ]);
    }

    /**
     * @Route("/muscade/job/{id}/edit", name="admin_job_edit")
     * @param JobPost $jobPost
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */

    public function edit(JobPost $jobPost, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(JobType::class, $jobPost);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($jobPost);
            $manager->flush();

            $this->addFlash(
                'success',
                "succeffully edited job post: " . $jobPost->getTitle() . " ID: " . $jobPost->getId());
        }


        return $this->render('admin/job/edit.html.twig', [
            'title' => 'edit job',
            'job' => $jobPost,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/muscade/job/{id}/delete", name="admin_job_delete")
     * @param JobPost $jobPost
     * @param ObjectManager $manager
     * @return Response
     */

    public function delete(JobPost $jobPost, ObjectManager $manager)
    {
        $manager->remove($jobPost);
        $manager->flush();

        $this->addFlash('success', 'succeffully deleted ' . $jobPost->getTitle() . " ID: " . $jobPost->getId());

        return $this->redirectToRoute('admin_jobs');
    }
}