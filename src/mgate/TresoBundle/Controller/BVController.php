<?php

namespace mgate\TresoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use mgate\TresoBundle\Entity\BV;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\TresoBundle\Form\BVType;

class BVController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bvs = $em->getRepository('mgateTresoBundle:BV')->findAll();
        
        return $this->render('mgateTresoBundle:BV:index.html.twig', array('bvs' => $bvs));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$bv= $em->getRepository('mgateTresoBundle:BV')->find($id)) {
            $bv = new BV;
            $bv->setBaseURSSAF(38.12)
                ->setTauxJuniorAssietteDeCotisation(0.2985)
                ->setTauxJuniorRemunerationBrute(0.043)
                ->setTauxEtudiantAssietteDeCotisation(0.158)
                ->setTauxEtudiantRemunerationBrute(0.024)
                ->setTypeDeTravail('Réalisateur')
                ->setDateDeVersement(new \DateTime('now'))
                ->setDateDemission(new \DateTime('now'));
        }

        $form = $this->createForm(new BVType, $bv);

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            if( $form->isValid() )
            {
                $em->persist($bv);                
                $em->flush();
                
                var_dump($bv);
                return $this->redirect($this->generateUrl('mgateTreso_BV_index', array()));
            }
        }

        return $this->render('mgateTresoBundle:BV:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'bv' =>$bv,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$bv= $em->getRepository('mgateTresoBundle:BV')->find($id))
            throw $this->createNotFoundException('Le BV n\'existe pas !');

        $em->remove($bv);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_BV_index', array()));


    }
    
    
    
    
    
    
}
