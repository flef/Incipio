<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Entity\Cc;
use mgate\SuiviBundle\Form\CcType;

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
       
    public function redigerAction($id)
    {
        
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        if(!$cc = $etude->getCc())
        {
            $cc = new Cc;
            $etude->setCc($cc);
        }
        
        $form = $this->createForm(new CcType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $this->get('mgate.doctype_manager')->checkSaveNewEmploye($etude->getCc());
                
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
