<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\SuiviBundle\Form\EtudeType;

use mgate\SuiviBundle\Entity\Suivi;
use mgate\SuiviBundle\Form\SuiviType;
use mgate\SuiviBundle\Form\SuiviHandler;

use mgate\SuiviBundle\Form\FactureHandler;


use mgate\SuiviBundle\Entity\Pvr;
use mgate\SuiviBundle\Form\PvrHandler;
use mgate\SuiviBundle\Form\PvrType;

class PvrController extends Controller
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
        
        
        $pvr = new Pvr;
        $pvr->setEtude($etude);
        $form        = $this->createForm(new PvrType, $pvr);
        $formHandler = new PvrHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
            if($this->get('request')->get('fs'))
            {
               
                return $this->redirect($this->generateUrl('mgateSuivi_fs_ajouter',array('id' => $etude->getId())));
            }
            else
            {
                return $this->redirect( $this->generateUrl('mgateSuivi_pvr_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Pvr:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    

    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Pvr')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cc entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Pvr:voir.html.twig', array(
            'pvr'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $pvr = $em->getRepository('mgate\SuiviBundle\Entity\Pvr')->find($id) )
        {
            throw $this->createNotFoundException('Pvr[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new PvrType, $pvr);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                if($this->get('request')->get('fs'))
                {
               
                    $em->flush();
                    return $this->redirect($this->generateUrl('mgateSuivi_pvr_ajouter',array('id' => $pvr->getId())));
                }
                else
                {
                    $em->flush();
                    return $this->redirect( $this->generateUrl('mgateSuivi_pvr_voir', array('id' => $pvr->getId())) );
                }
                
               
            }
                
        }

        return $this->render('mgateSuiviBundle:Pvr:modifier.html.twig', array(
            'form' => $form->createView(),
            'pvr' => $pvr,
        ));
    }
}
