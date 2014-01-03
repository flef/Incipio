<?php

namespace mgate\TresoBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;


use \mgate\TresoBundle\Entity\NoteDeFrais as NoteDeFrais;
use \mgate\TresoBundle\Entity\NoteDeFraisDetail as NoteDeFraisDetail;
use mgate\TresoBundle\Form\NoteDeFraisType as NoteDeFraisType;

class NoteDeFraisController extends Controller
{
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $nfs = $em->getRepository('mgateTresoBundle:NoteDeFrais')->findAll();
        
        return $this->render('mgateTresoBundle:NoteDeFrais:index.html.twig', array('nfs' => $nfs));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();
        if(!$nf = $em->getRepository('mgateTresoBundle:NoteDeFrais')->find($id))
            throw $this->createNotFoundException('La Note de Frais n\'existe pas !');
        
        return $this->render('mgateTresoBundle:NoteDeFrais:voir.html.twig', array('nf' => $nf));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$nf= $em->getRepository('mgateTresoBundle:NoteDeFrais')->find($id)) {
            $nf = new NoteDeFrais;
            $now = new \DateTime("now");
            $nf->setDate($now);           
        }

        $form = $this->createForm(new NoteDeFraisType, $nf);
       
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                foreach($nf->getDetails() as $nfd){
                    $nfd->setNoteDeFrais($nf);
                }
                $em->persist($nf);                
                $em->flush();
                return $this->redirect($this->generateUrl('mgateTreso_NoteDeFrais_voir', array('id' => $nf->getId())));
            }
        }

        return $this->render('mgateTresoBundle:NoteDeFrais:modifier.html.twig', array(
                    'form' => $form->createView(),
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$nf= $em->getRepository('mgateTresoBundle:NoteDeFrais')->find($id))
            throw $this->createNotFoundException('La Note de Frais n\'existe pas !');

        $em->remove($nf);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_NoteDeFrais_index', array()));


    }
}
