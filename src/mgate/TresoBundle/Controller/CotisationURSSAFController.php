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
