<?php

namespace mgate\TresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\TresoBundle\Entity\CotisationURSSAF;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\TresoBundle\Form\CotisationURSSAFType;

class CotisationURSSAFController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $cotisations = $em->getRepository('mgateTresoBundle:CotisationURSSAF')->findAll();
        
        return $this->render('mgateTresoBundle:CotisationURSSAF:index.html.twig', array('cotisations' => $cotisations));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$cotisation= $em->getRepository('mgateTresoBundle:CotisationURSSAF')->find($id)) {
            $cotisation = new CotisationURSSAF;
        }

        $form = $this->createForm(new CotisationURSSAFType, $cotisation);

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            if( $form->isValid() )
            {
                $em->persist($cotisation);                
                $em->flush();

                return $this->redirect($this->generateUrl('mgateTreso_CotisationURSSAF_index', array()));
            }
        }

        return $this->render('mgateTresoBundle:CotisationURSSAF:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'cotisation' =>$cotisation,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$cotisation= $em->getRepository('mgateTresoBundle:CotisationURSSAF')->find($id))
            throw $this->createNotFoundException('La Cotisation URSSAF n\'existe pas !');

        $em->remove($cotisation);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_CotisationURSSAF_index', array()));


    }
    
}
