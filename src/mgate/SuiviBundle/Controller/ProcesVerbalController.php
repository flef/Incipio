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
use mgate\SuiviBundle\Entity\ProcesVerbal;
use mgate\SuiviBundle\Form\ProcesVerbalType;
use mgate\SuiviBundle\Form\ProcesVerbalSubType;


class ProcesVerbalController extends Controller
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
    public function voirAction($id)
    {
       $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:ProcesVerbal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProcesVerbal entity.');
        }
		
		$etude = $entity->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:ProcesVerbal:voir.html.twig', array(
            'procesverbal'      => $entity,
            /*'delete_form' => $deleteForm->createView(),  */      ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) ) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
		
        $proces = new ProcesVerbal;
        $etude->addPvi($proces);
        
        $form = $this->createForm(new ProcesVerbalSubType, $proces, array('type' => 'pvi', 'prospect' => $etude->getProspect(),'phases' => count($etude->getPhases()->getValues())));      
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));

            if( $form->isValid() )
            {
                $em->persist($proces);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgateSuivi_procesverbal_voir', array('id' => $proces->getId())) );
            }
        }

        return $this->render('mgateSuiviBundle:ProcesVerbal:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
    }
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id_pv)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $procesverbal = $em->getRepository('mgate\SuiviBundle\Entity\ProcesVerbal')->find($id_pv) )
            throw $this->createNotFoundException('Le Procès Verbal n\'existe pas !');
			
		$etude = $procesverbal->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $form = $this->createForm(new ProcesVerbalSubType, $procesverbal, array('type' => $procesverbal->getType(), 'prospect' => $procesverbal->getEtude()->getProspect(), 'phases' => count($procesverbal->getEtude()->getPhases()->getValues())));   
        $deleteForm = $this->createDeleteForm($id_pv);
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                
                $em->persist($procesverbal);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_procesverbal_voir', array('id' => $procesverbal->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:ProcesVerbal:modifier.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'etude' => $procesverbal->getEtude(),
            'type' => $procesverbal->getType(),
            'procesverbal' => $procesverbal,
        ));
    }
    
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id_etude, $type)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id_etude) )
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        if(!$procesverbal = $etude->getDoc($type))
        {
            $procesverbal = new ProcesVerbal;
            if(strtoupper($type)=="PVR")
            {
                $etude->setPvr($procesverbal);
            }

            $procesverbal->setType($type);
        }
        
        $form = $this->createForm(new ProcesVerbalType, $etude, array('type' => $type, 'prospect' => $etude->getProspect(), 'phases' => count($etude->getPhases()->getValues())));   
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
            
            if( $form->isValid() )
            {
                
                $em->persist($etude);
                $em->flush();
                return $this->redirect( $this->generateUrl('mgateSuivi_procesverbal_voir', array('id' => $procesverbal->getId())) );
            }
                
        }

        return $this->render('mgateSuiviBundle:ProcesVerbal:rediger.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
            'type' => $type,
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function deleteAction($id_pv)
    {
        $form = $this->createDeleteForm($id_pv);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
   
            if( ! $entity = $em->getRepository('mgate\SuiviBundle\Entity\ProcesVerbal')->find($id_pv) )
                throw $this->createNotFoundException('Le Procès Verbal n\'existe pas !');
				
			$etude = $entity->getEtude();
		
			if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');


            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $entity->getNumero())));
    }

    private function createDeleteForm($id_pv)
    {
        return $this->createFormBuilder(array('id' => $id_pv))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
}
