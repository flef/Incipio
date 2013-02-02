<?php

namespace mgate\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('mgateCommentBundle:Default:index.html.twig', array('name' => $name));
    }
    
    
    public function maintenanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        foreach ($etudes as $entity) {
            $this->container->get('mgate_comment.thread')->creerThread("prospect_", $this->container->get('router')->generate('mgatePersonne_prospect_voir', array('id' => $entity->getId())), $entity);
        }
        
        
        return $this->render('mgateCommentBundle:Default:index.html.twig', array('name' => 'rien'));
    }
}
