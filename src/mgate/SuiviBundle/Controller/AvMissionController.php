<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Entity\AvMission;
use mgate\SuiviBundle\Form\AvMissionHandler;
use mgate\SuiviBundle\Form\AvMissionType;

class AvMissionController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }  

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }
        
        
        $avmission = new AvMission;
        $avmission->setEtude($etude);
        $form        = $this->createForm(new AvMissionType, $avmission);
        $formHandler = new AvMissionHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
           
            return $this->redirect( $this->generateUrl('mgateSuivi_avmission_voir', array('id' => $avmission->getId())) );
            
        }

        return $this->render('mgateSuiviBundle:AvMission:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:AvMission')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvMission entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:AvMission:voir.html.twig', array(
            'avmission'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $avmission = $em->getRepository('mgate\SuiviBundle\Entity\AvMission')->find($id) )
        {
            throw $this->createNotFoundException('AvMission[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new AvMissionType, $avmission);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_avmission_voir', array('id' => $avmission->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:AvMission:modifier.html.twig', array(
            'form' => $form->createView(),
            'avmission' => $avmission,
        ));
    }
}
