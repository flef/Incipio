<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


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
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $bv = $em->getRepository('mgateTresoBundle:BV')->find($id);
        
        return $this->render('mgateTresoBundle:BV:voir.html.twig', array('bv' => $bv));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$bv= $em->getRepository('mgateTresoBundle:BV')->find($id)) {
            $bv = new BV;
            $bv ->setTypeDeTravail('Réalisateur')
                ->setDateDeVersement(new \DateTime('now'))
                ->setDateDemission(new \DateTime('now'));
        }

        $form = $this->createForm(new BVType, $bv);

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            if( $form->isValid() )
            {
                $bv->setCotisationURSSAF();
                $charges = $em->getRepository('mgateTresoBundle:CotisationURSSAF')->findAllByDate($bv->getDateDemission());
                foreach ($charges as $charge)
                    $bv->addCotisationURSSAF($charge);
                
                $baseURSSAF = $em->getRepository('mgateTresoBundle:BaseURSSAF')->findByDate($bv->getDateDemission());
                $bv->setBaseURSSAF($baseURSSAF);
                
                $em->persist($bv);                
                $em->flush();

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
