<?php

namespace mgate\NavbarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $em = $this->getDoctrine()->getManager();
          
        $user = $this->container->get('security.context')->getToken()->getUser()->getPersonne();
        
        //Etudes Suiveur
        $etudesSuiveur = array();
        foreach($em->getRepository('mgateSuiviBundle:Etude')->findBy(array('suiveur' => $user), array('mandat'=> 'DESC', 'id'=> 'DESC')) as $etude)
        {
            $stateID = $etude->getStateID();
            if( $stateID <= 1 )
             array_push($etudesSuiveur, $etude);
        }
        
        return $this->render('NavbarBundle::layout.html.twig', array('etudesSuiveur' => $etudesSuiveur));
    }
}
