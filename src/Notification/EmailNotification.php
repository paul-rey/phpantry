<?php


namespace App\Notification;


use App\Entity\Contact;
use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class EmailNotification
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    /**
     * ContactNotification constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $renderer
     */
    public function __construct(Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * @param Contact $contact
     * @throws
     */
    public function notifyMessage(Contact $contact)
    {
        $message = (new Swift_Message('Contact'))
            ->setFrom($contact->getEmail())
            ->setTo('admin@phpantry.com')
            ->setReplyTo($contact->getEmail())
            ->setBody(
                $this->renderer->render('emails/contact.html.twig', [
                    'contact' => $contact
                ]), 'text/html');

        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @throws
     */

    public function notifyRegistration(User $user)
    {
        $message = (new Swift_Message('confirmation registration'))
            ->setFrom('admin@phpantry.com')
            ->setTo($user->getEmail())
            ->setReplyTo('admin@phpantry.com')
            ->setBody(
                $this->renderer->render('emails/registration.html.twig', [
                    'user' => $user
                ]), 'text/html');

        $this->mailer->send($message);

    }

    /**
     * @param User $user
     * @throws
     */

    public function notifyPasswordRecovery(User $user)
    {
        $message = (new Swift_Message('Password Recovery'))
            ->setFrom('admin@phpantry.com')
            ->setTo($user->getEmail())
            ->setReplyTo('admin@phpantry.com')
            ->setBody(
                $this->renderer->render('emails/password_recovery.html.twig', [
                    'user' => $user
                ]), 'text/html');

        $this->mailer->send($message);
    }
}