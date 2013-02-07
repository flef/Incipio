<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Form\MembreType;

class MembreController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function ajouterAction()
    {
        $em = $this->getDoctrine()->getEntityManager();    
        
        $membre = new Membre;

        $form = $this->createForm(new MembreType, $membre);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($membre);    
                $em->flush();
                $membre->getPersonne()->setMembre($membre);
                $em->flush();

                return $this->redirect( $this->generateUrl('mgatePersonne_membre_voir', array('id' => $membre->getId())) );
            }
        }

        return $this->render('mgatePersonneBundle:Membre:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Membre')->findAll();

        return $this->render('mgatePersonneBundle:Membre:index.html.twig', array(
            'membres' => $entities,
        ));
                
    }
    
    /**
     * @Secure(roles="ROLE_ELEVE")
     */     
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Membre')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Membre entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Membre:voir.html.twig', array(
            'membre'      => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $membre = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($id) )
        {
            throw $this->createNotFoundException('Membre [id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new MembreType, $membre);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($membre);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgatePersonne_membre_voir', array('id' => $membre->getId())) );
            }
        }


        return $this->render('mgatePersonneBundle:Membre:modifier.html.twig', array(
            'form' => $form->createView(),
            'membre'      => $membre,
        ));
    }
}
