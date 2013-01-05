<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;
use mgate\SuiviBundle\Entity\Cc;
use mgate\SuiviBundle\Form\CcType;
use mgate\SuiviBundle\Form\CcHandler;

class CcController extends Controller
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
        
        
        $cc = new Cc;
        $cc->setEtude($etude);
        $form        = $this->createForm(new CcType, $cc);
        $formHandler = new CcHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
            if($this->get('request')->get('pvi'))
            {
               
                return $this->redirect($this->generateUrl('mgateSuivi_pvi_ajouter',array('id' => $etude->getId())));
            }
            else
            {
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Cc:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Cc')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cc entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Cc:voir.html.twig', array(
            'cc'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Cc[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new CcType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_cc_voir', array('id' => $etude->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Cc:modifier.html.twig', array(
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

        $form        = $this->createForm(new CcType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Cc:rediger.html.twig', array(
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
        
        
        $cc = $etude->getCc();
        $version = $etude->getCc()->getVersion();
        $dateSignature = $etude->getCc()->getDateSignature(); 
        $acompte = $etude->getAcompte();
        $pourcentageAcompte = $etude->getPourcentageAcompte();
        
        $test = array( 
            'Version' => $version,
            'Acompte' => $acompte,
            'Pourcentage Acompte' => $pourcentageAcompte,
            'Date de signature' => $dateSignature);
        
        $etude->getCc()->setGenerer(1);//initialisation avant test
      

        foreach($test as $cle => $element)
        {
            if(empty($element)) 
            {
               $etude->getCc()->setGenerer(0);
               $manquant[]=$cle;
            }
        }

         $manquant[]="0"; // nécessaire pour l'initialiser si generer=1    
         $generer = $etude->getCc()->getGenerer();// ne pas bouger car on doit récupérer la valeur de générer après vérification
        
         return $this->render('mgateSuiviBundle:Cc:generer.html.twig', array(
             'cc' => $cc,
             'manquants' => $manquant,
             'etude'=> $etude // pour moi faut transmettre que ça, m'enfin
             ));
        
        
    }
}
