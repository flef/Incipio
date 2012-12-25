<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;

use mgate\SuiviBundle\Entity\FactureAcompte;

use mgate\SuiviBundle\Form\FactureAcompteType;
use mgate\SuiviBundle\Entity\Pvr;
use mgate\SuiviBundle\Form\PvrType;

class FactureAcompteController extends Controller
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
        
        // On vérifie que le prospect existe bien
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Prospect[id='.$id.'] inexistant');
        }
        
        
       // $factureacompte = new Employe;
       // $employe->setProspect($prospect);

        $form        = $this->createForm(new FactureAcompteType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($etude);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgateSuivi_factureacompte_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:FactureAcompte:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    

    public function voirAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:FactureAcompte')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cc entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:FactureAcompte:voir.html.twig', array(
            'facture'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        // On vérifie que le prospect existe bien
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Prospect[id='.$id.'] inexistant');
        }
        
        
       // $factureacompte = new Employe;
       // $employe->setProspect($prospect);

        $form        = $this->createForm(new FactureAcompteType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($etude);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgateSuivi_factureacompte_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:FactureAcompte:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
