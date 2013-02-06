<?php

namespace mgate\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\UserBundle\Form\UserAdminType;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('mgateUserBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function listerAction()
    {
        $em = $this->getDoctrine()->getManager();
        

        $entities = $em->getRepository('mgateUserBundle:User')->findAll();
                
        return $this->render('mgateUserBundle:Default:lister.html.twig', array('users' => $entities));
    }
    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('mgateUserBundle:User')->find($id); // Ligne qui posse problème
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        return $this->render('mgateUserBundle:Default:voir.html.twig', array('user' => $user));
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('mgateUserBundle:User')->find($id); // Ligne qui posse problème
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $form = $this->createForm(new UserAdminType('mgate\UserBundle\Entity\User'), $user);
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
            
            if( $form->isValid() )
            {
                
                $em->persist($user);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgate_user_voir', array('id' => $user->getId())) );
            }
                
        }
        
        return $this->render('mgateUserBundle:Default:modifier.html.twig', array('form' => $form->createView()));       
        
    }
    
}
