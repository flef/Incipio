<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\PersonneBundle\Entity\User;
use mgate\PersonneBundle\Form\UserType;
use mgate\PersonneBundle\Form\UserHandler;

class UserController extends Controller
{
    
    public function ajouterAction()
    {
        $user = new User;

        $form        = $this->createForm(new UserType, $user);
        $formHandler = new UserHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgatePersonne_user_voir', array('id' => $user->getId())) );
        }

        return $this->render('mgatePersonneBundle:User:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:User')->findAll();

        return $this->render('mgatePersonneBundle:User:index.html.twig', array(
            'users' => $entities,
        ));
                
    }
    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:User:voir.html.twig', array(
            'user'      => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $user = $em->getRepository('mgate\PersonneBundle\Entity\User')->find($id) )
        {
            throw $this->createNotFoundException('User [id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new UserType, $user);
        $formHandler = new UserHandler($form, $this->get('request'), $em);

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgatePersonne_user_voir', array('id' => $user->getId())) );
        }

        return $this->render('mgatePersonneBundle:User:modifier.html.twig', array(
            'form' => $form->createView(),
            'user'      => $user,
        ));
    }
}
