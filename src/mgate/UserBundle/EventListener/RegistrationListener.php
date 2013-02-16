<?php
namespace mgate\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegistrationListener implements EventSubscriberInterface
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onRegistrationConfirmed',
        );
    }

    // Prévenir lorsque quelqu'un valide compte
    public function onRegistrationConfirmed(FilterUserResponseEvent $event)
    {

        $message = \Swift_Message::newInstance()
            ->setSubject('My M-GaTE: Nouvel utilisateur '.$event->getUser()->getUsername())
            ->setFrom('mgate.je@gmail.com')
            ->setTo('stephane.collot@gmail.com')
            ->setBody($this->templating->render('mgateUserBundle:Default:alert-email.html.twig',
                                        array('username' => $event->getUser()->getUsername(), 'email' => $event->getUser()->getEmail())), 'text/html');
        $this->mailer->send($message);
        
    }
}