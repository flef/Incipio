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
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id); // Ligne qui posse problème
        $entity = $etude->getAp();
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
        
        
        
        $version = $etude->getAp()->getVersion();
        $dateSignature = $etude->getAp()->getDateSignature(); 
        $fraisDossier = $etude->getFraisDossier();
        $presentationProjet = $etude->getPresentationProjet();
        $descriptionPrestation = $etude->getDescriptionPrestation();
        $typePrestation = $etude->getTypePrestation();
        $competences = $etude->getCompetences();
        $phases = $etude->getPhases();// tester avec foreach
        $prospect = $etude->getProspect();// tester avec foreach
        $suiveur = $etude->getSuiveur();// tester avec boucle foreach
        $signataire1 = $etude->getAp()->getSignataire1(); //suiveur
        $signataire2 = $etude->getAp()->getSignataire2();// tester avec foreach
        $test = array( 
             'Version' => $version,
             'Frais de dossier'  => $fraisDossier,
             'Presentation du projet' => $presentationProjet,
             'Description de la prestation' => $descriptionPrestation,
             'Type de prestation' => $typePrestation,
             'Competences'  => $competences,
            'Date de signature' => $dateSignature);
        $testSignataire2=array( 'Prenom du signataire client' => $etude->getAp()->getSignataire2()->getPrenom(),
                                'Poste du signataire client' => $etude->getAp()->getSignataire2()->getPoste(),
                                'Nom du signataire client' => $etude->getAp()->getSignataire2()->getNom()
                              );
        $testSuiveur=array( 'Nom du suiveur' => $etude->getSuiveur()->getNom(),
                            'Prenom du suiveur' => $etude->getSuiveur()->getPrenom(),
                            'Mobile du suiveur' => $etude->getSuiveur()->getMobile(),
                            'Mail du suiveur' => $etude->getSuiveur()->getEmail()
                            );
        $testProspect=array( 'Nom du prospect' => $etude->getProspect()->getNom(),
                             'Entite' => $etude->getProspect()->getEntite(),
                             'Adresse du prospect' => $etude->getProspect()->getAdresse()
                           );
        $etude->getAp()->setGenerer(1);//initialisation avant test
        foreach($phases as $cle => $phase)
        {
            $testPhase = array( 'Nombre de JEH de la phase'=>$phase->getNbrJEH(),
                                'Prix du JEH de la phase'=>$phase->getPrixJEH(),
                                'Titre de la phase'=>$phase->getTitre(),
                                'Objectif de la phase'=>$phase->getObjectif(),
                                'Méthodologie de la phase'=>$phase->getMethodo(),
                                'Début de la phase'=>$phase->getDatedebut(),
                                'Délai de la phase'=>$phase->getDelai()
                                );
            
            foreach($testPhase as $cle => $element)
            {
                if(empty($element)) 
                {
                   $etude->getAp()->setGenerer(0);
                   $manquant[]=$cle;
                }
            }
        }

        foreach($test as $cle => $element)
        {
            if(empty($element)) 
            {
               $etude->getAp()->setGenerer(0);
               $manquant[]=$cle;
            }
        }
        foreach($testSuiveur as $cle => $element)
        {
            if(empty($element)) 
            {
               $etude->getAp()->setGenerer(0);
               $manquant[]=$cle;
            }
        }
        foreach($testProspect as $cle => $element)
        {
            if(empty($element)) 
            {
               $etude->getAp()->setGenerer(0);
               $manquant[]=$cle;
            }
        }
        foreach($testSignataire2 as $cle => $element)
        {
            if(empty($element)) 
            {
               $etude->getAp()->setGenerer(0);
               $manquant[]=$cle;
            }
        }

         $manquant[]="0"; // nécessaire pour l'initialiser si generer=1    
         $generer = $etude->getAp()->getGenerer();// ne pas bouger car on doit récupérer la valeur de générer après vérification
        
         return $this->render('mgateSuiviBundle:Ap:generer.html.twig', array(
            'suiveur'        => $suiveur,
            'prospect'       => $prospect,
             'version' => $version,
             'dateSignature' => $dateSignature,
             'fraisDossier'  => $fraisDossier,
             'presentationProjet' => $presentationProjet,
             'descriptionPrestation' => $descriptionPrestation,
             'typePrestation' => $typePrestation,
             'competences'  => $competences,
             'phases'       => $phases,
             'generer' => $generer,
             'signataire2' => $signataire2,
             'manquants' => $manquant,
             'etude'=> $etude // pour moi faut transmettre que ça, m'enfin
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
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Ap:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
}
