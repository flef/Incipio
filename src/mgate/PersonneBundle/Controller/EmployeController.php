<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PersonneBundle\Entity\Employe;
use mgate\PersonneBundle\Form\EmployeType;

class EmployeController extends Controller
{

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function ajouterAction($prospect_id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        
        // On vérifie que le prospect existe bien
        if( ! $prospect = $em->getRepository('mgate\PersonneBundle\Entity\Prospect')->find($prospect_id) )
        {
            throw $this->createNotFoundException('Prospect[id='.$prospect_id.'] inexistant');
        }
        
        
        $employe = new Employe;
        $employe->setProspect($prospect);

        $form        = $this->createForm(new EmployeType, $employe);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($employe);    
                $em->flush();
                $employe->getPersonne()->setEmploye($employe);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgatePersonne_employe_voir', array('id' => $employe->getId())) );
            }
        }

        return $this->render('mgatePersonneBundle:Employe:ajouter.html.twig', array(
            'form' => $form->createView(),
            'prospect' => $prospect,
            'format' => $format,
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Employe')->findAll();

        return $this->render('mgatePersonneBundle:Employe:index.html.twig', array(
            'users' => $entities,
        ));
                
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Employe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employe entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Employe:voir.html.twig', array(
            'employe'      => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */ 
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $employe = $em->getRepository('mgate\PersonneBundle\Entity\Employe')->find($id) )
        {
            throw $this->createNotFoundException('Employe [id='.$id.'] inexistant');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new EmployeType, $employe);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($employe);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgatePersonne_employe_voir', array('id' => $employe->getId())) );
            }
        }


        return $this->render('mgatePersonneBundle:Employe:modifier.html.twig', array(
            'form' => $form->createView(),
            'employe'      => $employe,
        ));
    }
}
