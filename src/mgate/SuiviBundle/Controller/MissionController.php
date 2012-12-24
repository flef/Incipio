<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;
use mgate\SuiviBundle\Entity\Mission;
use mgate\SuiviBundle\Form\MissionType;
use mgate\SuiviBundle\Form\MissionHandler;

class MissionController extends Controller
{
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
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
        
        
        $mission = new Mission;
        $mission->setEtude($etude);
        $form        = $this->createForm(new MissionType, $mission);
        $formHandler = new MissionHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
           
            return $this->redirect( $this->generateUrl('mgateSuivi_mission_voir', array('id' => $mission->getId())) );
            
        }

        return $this->render('mgateSuiviBundle:Mission:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Mission')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvMission entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Mission:voir.html.twig', array(
            'mission'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $mission = $em->getRepository('mgate\SuiviBundle\Entity\Mission')->find($id) )
        {
            throw $this->createNotFoundException('Mission[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new MissionType, $mission);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_mission_voir', array('id' => $mission->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Mission:modifier.html.twig', array(
            'form' => $form->createView(),
            'mission' => $mission,
        ));
    }
}
