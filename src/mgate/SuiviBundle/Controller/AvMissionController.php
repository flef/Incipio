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
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Entity\AvMission;
use mgate\SuiviBundle\Form\AvMissionHandler;
use mgate\SuiviBundle\Form\AvMissionType;

class AvMissionController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }  

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        // On vérifie que l'article d'id $id existe bien, sinon, erreur 404.
        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
        
        
        $avmission = new AvMission;
        $avmission->setEtude($etude);
        $form        = $this->createForm(new AvMissionType, $avmission);
        $formHandler = new AvMissionHandler($form, $this->get('request'), $em);
        
        if($formHandler->process())
        {
           
            return $this->redirect( $this->generateUrl('mgateSuivi_avmission_voir', array('id' => $avmission->getId())) );
            
        }

        return $this->render('mgateSuiviBundle:AvMission:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:AvMission')->find($id); 

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AvMission entity.');
        }
		
		$etude = $entity->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:AvMission:voir.html.twig', array(
            'avmission'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $avmission = $em->getRepository('mgate\SuiviBundle\Entity\AvMission')->find($id) )
        {
            throw $this->createNotFoundException('AvMission[id='.$id.'] inexistant');
        }
		
		$etude = $avmission->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $form        = $this->createForm(new AvMissionType, $avmission);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_avmission_voir', array('id' => $avmission->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:AvMission:modifier.html.twig', array(
            'form' => $form->createView(),
            'avmission' => $avmission,
        ));
    }
}
