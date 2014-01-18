<?php

namespace mgate\TresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\TresoBundle\Entity\Compte;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\TresoBundle\Form\CompteType;

class CompteController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $comptes = $em->getRepository('mgateTresoBundle:Compte')->findAll();
        
        return $this->render('mgateTresoBundle:Compte:index.html.twig', array('comptes' => $comptes));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$compte= $em->getRepository('mgateTresoBundle:Compte')->find($id)) {
            $compte = new Compte;            
        }

        $form = $this->createForm(new CompteType, $compte);

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            if( $form->isValid() )
            {
                $em->persist($compte);                
                $em->flush();
                return $this->redirect($this->generateUrl('mgateTreso_Compte_index', array()));
            }
        }

        return $this->render('mgateTresoBundle:Compte:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'compte' =>$compte,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$compte= $em->getRepository('mgateTresoBundle:Compte')->find($id))
            throw $this->createNotFoundException('Le Compte n\'existe pas !');

        $em->remove($compte);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_Compte_index', array()));


    }
    
    
    
    
    
    
}
