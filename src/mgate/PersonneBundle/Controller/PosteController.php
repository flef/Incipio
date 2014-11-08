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

use mgate\PersonneBundle\Entity\Poste;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Form\PosteType;

class PosteController extends Controller
{
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */     
    public function ajouterAction()
    {
        $em = $this->getDoctrine()->getManager();    
        
        $poste = new Poste;

        $form = $this->createForm(new PosteType, $poste);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($poste);    
                $em->flush();
    
                return $this->redirect( $this->generateUrl('mgatePersonne_poste_voir', array('id' => $poste->getId())) );
            }
        }

        return $this->render('mgatePersonneBundle:Poste:ajouter.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Poste')->findAll();

        return $this->render('mgatePersonneBundle:Poste:index.html.twig', array(
            'postes' => $entities,
        ));
                
    }
    
    /**
     * @Secure(roles="ROLE_ELEVE")
     */    
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Poste')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Le poste demandé n\'existe pas !');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Poste:voir.html.twig', array(
            'poste'      => $entity,
            /*'delete_form' => $deleteForm->createView(),        */));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if( ! $poste = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->find($id) )
            throw $this->createNotFoundException('Le poste demandé n\'existe pas !');

        // On passe l'$article récupéré au formulaire
        $form        = $this->createForm(new PosteType, $poste);
        $deleteForm = $this->createDeleteForm($id);
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $em->persist($poste);    
                $em->flush();

                return $this->redirect( $this->generateUrl('mgatePersonne_poste_voir', array('id' => $poste->getId())) );
            }
        }


        return $this->render('mgatePersonneBundle:Poste:modifier.html.twig', array(
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */    
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
   
            if( ! $entity = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->find($id) )
                throw $this->createNotFoundException('Le poste demandé n\'existe pas !');
            
            foreach($entity->getMembres() as $membre)
                    $membre->setPoste(null);

            //$entity->setMembres(null);
            
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgatePersonne_poste_homepage'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
