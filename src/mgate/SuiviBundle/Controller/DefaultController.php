<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($name)
    {
        return $this->render('mgateSuiviBundle:Default:index.html.twig', array('name' => $name));
    }
}
