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


namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PersonneBundle\Entity\Employe;
use mgate\PersonneBundle\Form\EmployeType;

class EmployeController extends Controller
{

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function ajouterAction($prospect_id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        
        // On vérifie que le prospect existe bien
        if( ! $prospect = $em->getRepository('mgate\PersonneBundle\Entity\Prospect')->find($prospect_id) )
        {
            throw $this->createNotFoundException('Ce prospect n\'existe pas');
        }
        
        
        $employe = new Employe;
        $employe->setProspect($prospect);

        $form        = $this->createForm(new EmployeType, $employe);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($employe);    
                $em->flush();
                $employe->getPersonne()->setEmploye($employe);
                $em->flush();
                
                return $this->redirect( $this->generateUrl('mgatePersonne_employe_voir', array('id' => $employe->getId())) );
            }
        }

        return $this->render('mgatePersonneBundle:Employe:ajouter.html.twig', array(
            'form' => $form->createView(),
            'prospect' => $prospect,
            'format' => $format,
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Employe')->findAll();

        return $this->render('mgatePersonneBundle:Employe:index.html.twig', array(
            'users' => $entities,
        ));
                
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Employe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('L\'employé demandé n\'existe pas');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Employe:voir.html.twig', array(
            'employe'      => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */ 
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $employe = $em->getRepository('mgate\PersonneBundle\Entity\Employe')->find($id) )
        {
            throw $this->createNotFoundException('L\'employé demandé n\'existe pas');
        }

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new EmployeType, $employe);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($employe);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgatePersonne_employe_voir', array('id' => $employe->getId())) );
            }
        }


        return $this->render('mgatePersonneBundle:Employe:modifier.html.twig', array(
            'form' => $form->createView(),
            'employe'      => $employe,
        ));
    }
}
