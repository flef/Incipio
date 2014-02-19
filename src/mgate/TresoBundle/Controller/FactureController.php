<?php

namespace mgate\TresoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;


use \mgate\TresoBundle\Entity\Facture as Facture;
use \mgate\TresoBundle\Entity\FactureDetail as FactureDetail;
use mgate\TresoBundle\Form\FactureType as FactureType;

class FactureController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('mgateTresoBundle:Facture')->findAll();
        
        return $this->render('mgateTresoBundle:Facture:index.html.twig', array('factures' => $factures));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();
        if(!$facture = $em->getRepository('mgateTresoBundle:Facture')->find($id))
            throw $this->createNotFoundException('La Facture n\'existe pas !');
        
        return $this->render('mgateTresoBundle:Facture:voir.html.twig', array('facture' => $facture));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$facture= $em->getRepository('mgateTresoBundle:Facture')->find($id)) {
            $facture = new Facture;
            $now = new \DateTime("now");
            $facture->setDateEmission($now);           
        }

        $form = $this->createForm(new FactureType, $facture);
       
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                foreach($facture->getDetails() as $factured){
                    $factured->setFacture($facture);
                }
                $em->persist($facture);                
                $em->flush();
                return $this->redirect($this->generateUrl('mgateTreso_Facture_voir', array('id' => $facture->getId())));
            }
        }

        return $this->render('mgateTresoBundle:Facture:modifier.html.twig', array(
                    'form' => $form->createView(),
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$facture= $em->getRepository('mgateTresoBundle:Facture')->find($id))
            throw $this->createNotFoundException('La Facture n\'existe pas !');

        $em->remove($facture);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_Facture_index', array()));


    }
}
