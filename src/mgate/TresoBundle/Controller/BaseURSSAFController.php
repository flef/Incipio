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
use mgate\TresoBundle\Entity\BaseURSSAF;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\TresoBundle\Form\BaseURSSAFType;

class BaseURSSAFController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bases = $em->getRepository('mgateTresoBundle:BaseURSSAF')->findAll();
        
        return $this->render('mgateTresoBundle:BaseURSSAF:index.html.twig', array('bases' => $bases));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$base= $em->getRepository('mgateTresoBundle:BaseURSSAF')->find($id)) {
            $base = new BaseURSSAF;            
        }

        $form = $this->createForm(new BaseURSSAFType, $base);

        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            if( $form->isValid() )
            {
                $em->persist($base);                
                $em->flush();
                
                return $this->redirect($this->generateUrl('mgateTreso_BaseURSSAF_index', array()));
            }
        }

        return $this->render('mgateTresoBundle:BaseURSSAF:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'base' =>$base,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function supprimerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$base= $em->getRepository('mgateTresoBundle:BaseURSSAF')->find($id))
            throw $this->createNotFoundException('La base URSSAF n\'existe pas !');

        $em->remove($base);                
        $em->flush();
        return $this->redirect($this->generateUrl('mgateTreso_BaseURSSAF_index', array()));


    }
    
    
    
    
    
    
}
