<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/

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
        $junior = $this->container->getParameter('junior');
        $message = \Swift_Message::newInstance()
            ->setSubject('Incipio : Nouvel utilisateur '.$event->getUser()->getUsername())
            ->setFrom('no-reply@incipio.fr')
            ->setTo($junior['email'])
            ->setBody($this->templating->render('mgateUserBundle:Default:alert-email.html.twig',
                                        array('username' => $event->getUser()->getUsername(), 'email' => $event->getUser()->getEmail())), 'text/html');
        $this->mailer->send($message);
        
    }
}