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
