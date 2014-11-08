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
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Mandat;
use mgate\PersonneBundle\Form\MembreType;

class PersonneController extends Controller {
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function annuaireAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->findAll();
        
        return $this->render('mgatePersonneBundle:Personne:annuaire.html.twig', array(
                    'personnes' => $entities,
                ));
    }
    
   /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function listeMailAction(){
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->findAll();
        
        $membres = $em->getRepository('mgatePersonneBundle:Membre')->getCotisants();
        
        $cotisants = array();
        $cotisantsEtu = array();
        foreach ($membres as $cotisant){
            $nom = $cotisant->getPersonne()->getNom() . ' ' . $cotisant->getPersonne()->getPrenom();
            
            $mailEtu = $cotisant->getEmailEMSE();
            $mail = $cotisant->getPersonne()->getEmail();
            if($mail != null) $cotisants[$nom] = $mail;
            if($mailEtu != null) $cotisantsEtu[$nom] = $mailEtu;
        }
        ksort($cotisants);
        ksort($cotisantsEtu);
        
        $nbrCotisants = count($cotisants);
        $nbrCotisantsEtu = count($cotisantsEtu);

        $listCotisants = "";
        $listCotisantsEtu = "";
        foreach ($cotisants as $nom => $mail)
            $listCotisants .= "$nom <$mail>; ";
        foreach ($cotisantsEtu as $nom => $mail)
            $listCotisantsEtu .= "$nom <$mail>; ";
        
        
        
        return $this->render('mgatePersonneBundle:Personne:listeDiffusion.html.twig', array(
                'personnes' => $entities,
                'cotisants' => $listCotisants,
                'cotisantsEtu' => $listCotisantsEtu,
                'nbrCotisants' => $nbrCotisants,
                'nbrCotisantsEtu' => $nbrCotisantsEtu,
                    
                ));
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
   
           if( ! $entity = $em->getRepository('mgate\PersonneBundle\Entity\Personne')->find($id) )
                throw $this->createNotFoundException('La personne demandée n\'existe pas !');
            
            $em->remove($entity);
            $em->flush();
        
        return $this->redirect($this->generateUrl('mgatePersonne_annuaire'));
    }
    
    
    
}