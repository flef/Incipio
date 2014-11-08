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


namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\DomaineCompetence;
use mgate\SuiviBundle\Form\DomaineCompetenceType;


class DomaineCompetenceController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();        
        $entities = $em->getRepository('mgateSuiviBundle:DomaineCompetence')->findAll();

        $domaine = new DomaineCompetence;

        $form = $this->createForm(new DomaineCompetenceType(), $domaine);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
            	$em->persist($domaine);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_domaine_index'));
            }
        }

        return $this->render('mgateSuiviBundle:DomaineCompetence:index.html.twig', array(
            'domaines' => $entities,
            'form' => $form->createView(),
        ));  
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();        
        

        if(!$domaine = $em->getRepository('mgate\SuiviBundle\Entity\DomaineCompetence')->find($id) )
            throw $this->createNotFoundException('Ce domaine de competence n\'existe pas !');
        
        $em->remove($domaine);
        $em->flush();

		return $this->redirect( $this->generateUrl('mgateSuivi_domaine_index'));
    }


}
