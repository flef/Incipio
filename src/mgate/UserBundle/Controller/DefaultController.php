<?php

namespace mgate\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('mgateUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
