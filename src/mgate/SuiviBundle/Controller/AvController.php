<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;

use mgate\SuiviBundle\Entity\Av;
use mgate\SuiviBundle\Form\AvHandler;
use mgate\SuiviBundle\Form\AvType;

class AvController extends Controller
{
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Av:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }  
    
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }
        
        
        $av = new Av;
        $av->setEtude($etude);
        $form        = $this->createForm(new AvType, $av);
        $formHandler = new AvHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
           
            return $this->redirect( $this->generateUrl('mgateSuivi_av_voir', array('id' => $av->getId())) );
            
        }

        return $this->render('mgateSuiviBundle:Av:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }

    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Av')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cc entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Av:voir.html.twig', array(
            'av'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $av = $em->getRepository('mgate\SuiviBundle\Entity\Av')->find($id) )
        {
            throw $this->createNotFoundException('Av[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new AvType, $av);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                return $this->redirect( $this->generateUrl('mgateSuivi_av_voir', array('id' => $av->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Av:modifier.html.twig', array(
            'form' => $form->createView(),
            'av' => $av,
        ));
    }
}
