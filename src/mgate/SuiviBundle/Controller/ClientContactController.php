<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\ClientContactHandler;

use mgate\SuiviBundle\Entity\ClientContact;
use mgate\SuiviBundle\Form\ClientContactType;


class ClientContactController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }  
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }
        
        
        $clientcontact = new ClientContact;
        $clientcontact->setEtude($etude);
        $form        = $this->createForm(new ClientContactType, $clientcontact);
        $formHandler = new ClientContactHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
           
            return $this->redirect( $this->generateUrl('mgateSuivi_clientcontact_voir', array('id' => $clientcontact->getId())) );
            
        }

        return $this->render('mgateSuiviBundle:ClientContact:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:ClientContact')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvMission entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:ClientContact:voir.html.twig', array(
            'clientcontact'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $clientcontact = $em->getRepository('mgate\SuiviBundle\Entity\ClientContact')->find($id) )
        {
            throw $this->createNotFoundException('ClientContact[id='.$id.'] inexistant');
        }

        $form        = $this->createForm(new ClientContactType, $clientcontact);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_clientcontact_voir', array('id' => $clientcontact->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:ClientContact:modifier.html.twig', array(
            'form' => $form->createView(),
            'clientcontact' => $clientcontact,
        ));
    }
}
