<?php

namespace App\Controller;

use App\Entity\JobPost;
use App\Entity\JobSearch;
use App\Form\JobSearchType;
use App\Form\JobType;
use App\Repository\JobPostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    /**
     * @Route("/job", name="jobs_index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param JobPostRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, JobPostRepository $repository, Request $request)
    {

        $search = new JobSearch();
        $searchBar = $this->createForm(JobSearchType::class, $search);
        $searchBar->handleRequest($request);

        // Recupere les annonces grace a l'injection du JobPostRepository
        $jobs = $paginator->paginate(
            $repository->findJobCard($search),
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('job/index.html.twig', [
            'title' => 'job',
            'jobs' => $jobs,
            'searchBar' => $searchBar->createView(),
        ]);
    }

    /**
     * @Route("/job/new", name="jobs_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */

    public function create(Request $request, ObjectManager $manager)
    {
        $job = new JobPost();

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        $job->setUsername($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($job);
            $manager->flush();

            $this->addFlash(
                "success",
                $job->getTitle() . ' successfuly created'
            );

            return $this->redirectToRoute('jobs_show', [
                'slug' => $job->getSlug(),
                'id' => $job->getId()
            ]);
        }

        return $this->render('job/new.html.twig', [
            'title' => 'new',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/job/{slug}/{id}", name="jobs_show", methods={"GET"})
     * @param JobPost $job
     * @return Response
     */

    // Use param converter to find job list

    public function show(JobPost $job)
    {
        return $this->render('job/show.html.twig', [
            'title' => $job->getCompany().' | '.$job->getSlug(),
            'job' => $job
        ]);
    }

    /**
     * @Route("/account/job/{id}/edit", name="account_edit_job")
     * @Security("is_granted('ROLE_USER') and user === job.getUsername()")
     * @param Request $request
     * @param JobPost $job
     * @param ObjectManager $manager
     * @return Response
     */

    public function edit(Request $request, JobPost $job, ObjectManager $manager)
    {
//         remplace le security annotation mais redirige vers list job sans 403
//        if ($job->getUsername() !== $this->getUser()) {
//            return $this->redirectToRoute('jobs_index');
//        }

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($job);
            $manager->flush();

            $this->addFlash(
                "success",
                $job->getTitle() . ' successfuly edited'
            );

            return $this->redirectToRoute('account_index', [
                'slug' => $job->getSlug(),
                'id' => $job->getId()
            ]);
        }

        return $this->render('job/edit.html.twig', [
            'title' => 'edit',
            'job' => $job,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/job/{id}/delete", name="account_delete_job")
     * @Security("is_granted('ROLE_USER') and user == job.getUsername()")
     * @param JobPost $job
     * @param ObjectManager $manager
     * @return RedirectResponse
     */

    public function delete(JobPost $job, ObjectManager $manager)
    {

        $manager->remove($job);
        $manager->flush();

        $this->addFlash(
            "success",
            $job->getTitle() . ' successfuly removed'
        );

        return $this->redirectToRoute('account_index');
    }
}
