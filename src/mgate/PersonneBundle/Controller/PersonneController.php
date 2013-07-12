<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Mandat;
use mgate\PersonneBundle\Form\MembreType;

class PersonneController extends Controller {
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function annuaireAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->findAll();
        
        return $this->render('mgatePersonneBundle:Personne:annuaire.html.twig', array(
                    'personnes' => $entities,
                ));
    }
    
   /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function listeMailAction(){
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->findBy(array('estAbonneNewsletter' => true, 'emailEstValide' => true));
        
        
        return $this->render('mgatePersonneBundle:Personne:listeDiffusion.html.twig', array(
                    'personnes' => $entities,
                    
                ));
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
   
           if( ! $entity = $em->getRepository('mgate\PersonneBundle\Entity\Personne')->find($id) )
                throw $this->createNotFoundException('Prospect[id='.$id.'] inexistant');
            
            $em->remove($entity);
            $em->flush();
        
        return $this->redirect($this->generateUrl('mgatePersonne_annuaire'));
    }
    
    
    
}