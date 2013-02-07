<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PersonneBundle\Entity\Poste;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Form\PosteType;

class PosteController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function ajouterAction()
    {
        $em = $this->getDoctrine()->getEntityManager();    
        
        $poste = new Poste;

        $form = $this->createForm(new PosteType, $poste);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($poste);    
                $em->flush();
    
                return $this->redirect( $this->generateUrl('mgatePersonne_poste_voir', array('id' => $poste->getId())) );
            }
        }

        return $this->render('mgatePersonneBundle:Poste:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Poste')->findAll();

        return $this->render('mgatePersonneBundle:Poste:index.html.twig', array(
            'postes' => $entities,
        ));
                
    }
    
    /**
     * @Secure(roles="ROLE_ELEVE")
     */    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Poste')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Poste entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Poste:voir.html.twig', array(
            'poste'      => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $poste = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->find($id) )
        {
            throw $this->createNotFoundException('Poste [id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new PosteType, $poste);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($poste);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgatePersonne_poste_voir', array('id' => $poste->getId())) );
            }
        }


        return $this->render('mgatePersonneBundle:Poste:modifier.html.twig', array(
            'form' => $form->createView(),
            'poste'      => $poste,
        ));
    }
}
