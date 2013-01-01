<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;
use mgate\SuiviBundle\Entity\Ap;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Entity\Prospect;
use mgate\SuiviBundle\Entity\Personne;
use mgate\SuiviBundle\Entity\Employe;

use mgate\PersonneBundle\Entity\Employe;

use mgate\SuiviBundle\Form\ApType;
use mgate\SuiviBundle\Form\ApHandler;
use mgate\SuiviBundle\Form\DocTypeSuiviType;

class ApController extends Controller
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
        
        
        $ap = new Ap;
        $ap->setEtude($etude);
        $form        = $this->createForm(new ApType, $ap);
        $formHandler = new ApHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
            if($this->get('request')->get('next'))
            {
               
                return $this->redirect($this->generateUrl('mgateSuivi_cc_ajouter',array('id' => $etude->getId())));
            }
            else
            {
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Ap:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        
        //attention reflechir si faut passer id etude ou rester en id Ap
        // en fait y a 2 fonction voir
        // une pour voir le suivi
        // et une pour voir la redaction
        $entity = $em->getRepository('mgateSuiviBundle:Ap')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ap entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Ap:voir.html.twig', array(
            'ap'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new ApType, $etude);//transmettre etude pour ajouter champ de etude
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_ap_voir', array('id' => $etude->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Ap:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
    public function redigerAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new ApType, $etude);//transmettre etude pour ajouter champ de etude
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                if($etude->getAp()->isKnownSignataire2()) //(true === $etude->knownSignataire2)
                {
                    $etude->getAp()->setSignataire2($etude->getAp()->getKnownedSignataire2());
                }
                else
                {
                    $etude->getAp()->setSignataire2($etude->getAp()->getNewSignataire2());
                    
                    $employe = new Employe();
                    $employe->setPersonne($etude->getAp()->getSignataire2());
                    $employe->setProspect($etude->getProspect());
                    $etude->getAp()->getNewSignataire2()->setEmploye($employe);
                    $em->persist($employe);
                }
                
                
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Ap:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
    public function genererAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
       
        $ap = $etude->getAp();
        $suiveur = $etude->getSuiveur();
        $fraisDossier = $etude->getFraisDossier();
        $presentationProjet = $etude->getPresentationProjet();
        $descriptionPrestation = $etude->getDescriptionPrestation();
        $typePrestation = $etude->getTypePrestation();
        $competences = $etude->getCompetences();
        $phases = $etude->getPhases();
        $prospect = $etude->getProspect();
        $test = array(
            'suiveur'        => $suiveur,
            'prospect'       => $prospect,
             'ap' => $ap,
             'fraisDossier'  => $fraisDossier,
             'presentationProjet' => $presentationProjet,
             'descriptionPrestation' => $descriptionPrestation,
             'typePrestation' => $typePrestation,
             'competences'  => $competences,
             'phases'       => $phases);
      
        //1 - tout afficher
        
        //2 - vérifier si ils sont vides ou pas 
        // 3 - afficher le bouton de génération si tout est là 
        
         return $this->render('mgateSuiviBundle:Ap:generer.html.twig', array(
            'suiveur'        => $suiveur,
            'prospect'       => $prospect,
             'ap' => $ap,
             'fraisDossier'  => $fraisDossier,
             'presentationProjet' => $presentationProjet,
             'descriptionPrestation' => $descriptionPrestation,
             'typePrestation' => $typePrestation,
             'competences'  => $competences,
             'phases'       => $phases,
             'test' => $test
             ));
        
        
    }
    
    public function SuiviAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        $ap = $etude->getAp();
        $form        = $this->createForm(new DocTypeSuiviType, $ap);//transmettre etude pour ajouter champ de etude
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_ap_voir', array('id' => $etude->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Ap:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
}
