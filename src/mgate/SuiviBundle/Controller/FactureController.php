<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use mgate\SuiviBundle\Form\EtudeType;

use mgate\SuiviBundle\Entity\Facture;
use mgate\SuiviBundle\Form\FactureType;
use mgate\SuiviBundle\Entity\Pvr;
use mgate\SuiviBundle\Form\PvrType;

class FactureController extends Controller
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

        // On vÃ©rifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }
        
        
        $facture = new Facture;
        $facture->setEtude($etude);
        
        $form        = $this->createForm(new FactureType, $facture);      
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));

            if( $form->isValid() )
            {
                $em->persist($facture);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Facture:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function voirAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Facture')->find($id); // Ligne qui posse problÃ¨me

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Facture entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Facture:voir.html.twig', array(
            'facture'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $facture = $em->getRepository('mgate\SuiviBundle\Entity\Facture')->find($id) )
        {
            throw $this->createNotFoundException('Cc[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new FactureType, $facture);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Facture:modifier.html.twig', array(
            'form' => $form->createView(),
            'facture' => $facture,
        ));
    }
    
    
    public function redigerAction($id, $type, $keyFi)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }

        if(!$facture = $etude->getDoc($type))
        {
            $facture = new Facture;
            if($type=="fa")
                $etude->setFa($facture);
            if($type=="fs")
                $etude->setFs($facture);
        }
       
        $form = $this->createForm(new FactureType, $etude, array('type' => $type));
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_facture_voir', array('id' => $facture->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Facture:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
}
