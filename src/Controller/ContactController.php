<?php


namespace App\Controller;


use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\EmailNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{

    /**
     * @Route("/contact", name="contact_page")
     * @param Request $request
     * @param EmailNotification $notification
     * @return ResponseAlias
     */
    public function contact(Request $request, EmailNotification $notification)
    {
        //create contact form

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $notification->notifyMessage($contact);

            $this->addFlash('success', 'Your email was successfully sent');

//            comment for testing
            return $this->redirectToRoute("home_page");
        }


        return $this->render('pages/contact.html.twig', [
            'title' => 'contact',
            'form' => $form->createView()
        ]);
    }

}