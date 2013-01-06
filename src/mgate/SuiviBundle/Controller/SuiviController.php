<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Entity\Suivi;
use mgate\SuiviBundle\Form\SuiviType;
use mgate\SuiviBundle\Form\SuiviHandler;


class SuiviController extends Controller
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
        
        $suivi = new Suivi;
        $suivi->setEtude($etude);
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (is_object($user) && $user instanceof \mgate\UserBundle\Entity\User)
            $suivi->setFaitPar($user->getPersonne());
        
        $form        = $this->createForm(new SuiviType, $suivi);
        $formHandler = new SuiviHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
           
            return $this->redirect( $this->generateUrl('mgateSuivi_suivi_voir', array('id' => $suivi->getId())) );
            
        }

        return $this->render('mgateSuiviBundle:Suivi:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Suivi')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvMission entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Suivi:voir.html.twig', array(
            'suivi'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
        
    }
    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $suivi = $em->getRepository('mgate\SuiviBundle\Entity\Suivi')->find($id) )
        {
            throw $this->createNotFoundException('Suivi[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new SuiviType, $suivi);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_suivi_voir', array('id' => $suivi->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:Suivi:modifier.html.twig', array(
            'form' => $form->createView(),
            'suivi' => $suivi,
        ));
    }
}
