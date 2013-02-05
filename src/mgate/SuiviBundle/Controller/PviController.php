<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\SuiviBundle\Form\EtudeType;

use mgate\SuiviBundle\Entity\Pvi;
use mgate\SuiviBundle\Form\PviHandler;
use mgate\SuiviBundle\Form\PviType;

class PviController extends Controller
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
        
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        
        $pvi = new Pvi;
        $etude->addPvi($pvi);

        $form = $this->createForm(new PviType, $pvi, array('prospect' => $etude->getProspect()));      
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
                $em->persist($pvi);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgateSuivi_pvi_voir', array('id' => $pvi->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Pvi:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
        
    }
  
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Pvi')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cc entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Pvi:voir.html.twig', array(
            'pvi'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $pvi = $em->getRepository('mgate\SuiviBundle\Entity\Pvi')->find($id) )
        {
            throw $this->createNotFoundException('Pvi[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new PviType, $pvi);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                if($this->get('request')->get('pvr'))
                {
               
                    $em->flush();
                    return $this->redirect($this->generateUrl('mgateSuivi_pvr_ajouter',array('id' => $pvi->getId())));
                }
                else
                {
                    $em->flush();
                    return $this->redirect( $this->generateUrl('mgateSuivi_pvi_voir', array('id' => $pvi->getId())) );
                }
                
               
            }
                
        }

        return $this->render('mgateSuiviBundle:Pvi:modifier.html.twig', array(
            'form' => $form->createView(),
            'pvi' => $pvi,
        ));
    }
}
