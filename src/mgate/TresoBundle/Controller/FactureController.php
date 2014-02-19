<?php

namespace mgate\TresoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;


use \mgate\TresoBundle\Entity\FactureVente as FactureVente;
use \mgate\TresoBundle\Entity\FactureVenteDetail as FactureVenteDetail;
use mgate\TresoBundle\Form\FactureVenteType as FactureVenteType;

class FactureVenteController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fvs = $em->getRepository('mgateTresoBundle:FactureVente')->findAll();
        
        return $this->render('mgateTresoBundle:FactureVente:index.html.twig', array('fvs' => $fvs));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();
        if(!$fv = $em->getRepository('mgateTresoBundle:FactureVente')->find($id))
            throw $this->createNotFoundException('La Facture n\'existe pas !');
        
        return $this->render('mgateTresoBundle:FactureVente:voir.html.twig', array('fv' => $fv));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$fv= $em->getRepository('mgateTresoBundle:FactureVente')->find($id)) {
            $fv = new FactureVente;
            $now = new \DateTime("now");
            $fv->setDate($now);           
        }

        $form = $this->createForm(new FactureVenteType, $fv);
       
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                foreach($fv->getDetailsDeVente() as $fvd){
                    $fvd->setFactureVente($fv);
                }
                $em->persist($fv);                
                $em->flush();
                return $this->redirect($this->generateUrl('mgateTreso_FactureVente_voir', array('id' => $fv->getId())));
            }
        }

        return $this->render('mgateTresoBundle:FactureVente:modifier.html.twig', array(
                    'form' => $form->createView(),
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$fv= $em->getRepository('mgateTresoBundle:FactureVente')->find($id))
            throw $this->createNotFoundException('La Facture n\'existe pas !');

        $em->remove($fv);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_FactureVente_index', array()));


    }
}
