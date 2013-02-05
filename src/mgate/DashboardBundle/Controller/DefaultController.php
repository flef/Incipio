<?php

namespace mgate\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
            $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('mgate.je@gmail.com')
        ->setTo('stephane.collot@gmail.com')
        ->setBody("test" )
    ;
    $this->get('mailer')->send($message);
    
    echo "loool";
        
        
        return $this->render('mgateDashboardBundle:Default:index.html.twig');
    }
}
