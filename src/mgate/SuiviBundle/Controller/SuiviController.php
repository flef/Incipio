<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\EtudeHandler;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Form\ApType;
use mgate\SuiviBundle\Form\ApHandler;
use mgate\SuiviBundle\Entity\Cc;
use mgate\SuiviBundle\Form\CcType;
use mgate\SuiviBundle\Form\CcHandler;
use mgate\SuiviBundle\Entity\Mission;
use mgate\SuiviBundle\Form\MissionType;
use mgate\SuiviBundle\Form\MissionHandler;
use mgate\SuiviBundle\Entity\Suivi;
use mgate\SuiviBundle\Form\SuiviType;
use mgate\SuiviBundle\Form\SuiviHandler;
use mgate\SuiviBundle\Entity\ClientContact;
use mgate\SuiviBundle\Form\ClientContactHandler;
use mgate\SuiviBundle\Form\ClientContactType;
use mgate\SuiviBundle\Entity\Pvi;
use mgate\SuiviBundle\Form\PviHandler;
use mgate\SuiviBundle\Form\PviType;
use mgate\SuiviBundle\Entity\Av;
use mgate\SuiviBundle\Form\AvHandler;
use mgate\SuiviBundle\Form\AvType;
use mgate\SuiviBundle\Entity\AvMission;
use mgate\SuiviBundle\Form\AvMissionHandler;
use mgate\SuiviBundle\Form\AvMissionType;
use mgate\SuiviBundle\Entity\Facture;
use mgate\SuiviBundle\Form\FactureHandler;
use mgate\SuiviBundle\Form\FactureType;
use mgate\SuiviBundle\Entity\Pvr;
use mgate\SuiviBundle\Form\PvrHandler;
use mgate\SuiviBundle\Form\PvrType;

class SuiviController extends Controller
{
    
    public function addEtudeAction()
    {
        $etude = new Etude;
        
        $form        = $this->createForm(new EtudeType, $etude);
        $formHandler = new EtudeHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());
        echo 'caca';
        
        if($formHandler->process())
        {
            var_dump($this->get('request')->request->get('envoie'));
           
            if($this->get('request')->get('next'))
            {
               
                return $this->redirect($this->generateUrl('mgateSuivi_ap_ajouter',array('id' => $etude->getId())));
            }
            else
            {
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
            
            //$ap = new Ap;
            //$form_suivant = $this->createForm(new ApType,$ap);
            //return $this->redirect($this->generateUrl('route de l'ap', array('id...));
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addApAction($id)
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
               
                return $this->redirect($this->generateUrl('mgateSuivi_cc_ajouter_cc',array('id' => $etude->getId())));
            }
            else
            {
                return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addCcAction()
    {
        
        
        $cc = new Cc;

        $form        = $this->createForm(new CcType, $cc);
        $formHandler = new CcHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $cc->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addMissionAction()
    {
        $mission = new Mission;

        $form        = $this->createForm(new MissionType, $mission);
        $formHandler = new MissionHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $mission->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addSuiviAction()
    {
        $suivi = new Suivi;

        $form        = $this->createForm(new SuiviType, $suivi);
        $formHandler = new SuiviHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $suivi->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addClientContactAction()
    {
        $clientcontact = new ClientContact;

        $form        = $this->createForm(new ClientContactType, $clientcontact);
        $formHandler = new ApHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $clientcontact->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addPviAction()
    {
        $pvi = new Pvi;

        $form        = $this->createForm(new PviType, $pvi);
        $formHandler = new PviHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $pvi->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addAvAction()
    {
        $av = new Av;

        $form        = $this->createForm(new AvType, $av);
        $formHandler = new AvHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $av->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addAvMissionAction()
    {
        $avmission = new AvMission;

        $form        = $this->createForm(new AvMissionType, $avmission);
        $formHandler = new AvMissionHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $avmission->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addFactureAction()
    {
        $facture = new Facture;

        $form        = $this->createForm(new FactureType, $facture);
        $formHandler = new FactureHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $facture->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function addPvrAction()
    {
        $pvr = new Pvr;

        $form        = $this->createForm(new PvrType, $pvr);
        $formHandler = new FactureHandler($form, $this->get('request'), $this->getDoctrine()->getEntityManager());

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $pvr->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
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

        $entity = $em->getRepository('mgateSuiviBundle:Etude')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Etude entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Etude:voir.html.twig', array(
            'etude'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new EtudeType, $etude);
        $formHandler = new EtudeHandler($form, $this->get('request'), $em);

        if($formHandler->process())
        {
            return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );
        }

        return $this->render('mgateSuiviBundle:Etude:modifier.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
}
