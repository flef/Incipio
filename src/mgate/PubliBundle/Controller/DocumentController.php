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

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Form\DocumentType;

class DocumentController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePubliBundle:Document')->findAll();
        
        $totalSize = 0;
        foreach ($entities as $entity){
            $totalSize += $entity->getSize();
        }
      
        return $this->render('mgatePubliBundle:Document:index.html.twig', array(
            'docs'       => $entities,
            'totalSize'  => $totalSize, 
        ));       
    }    

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function uploadEtudeAction($etude_id){
        $em = $this->getDoctrine()->getManager();
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->findByNumero($etude_id);

        if (!$etude)
            throw $this->createNotFoundException('Le document ne peut être lié à une étude qui n\'existe pas!');
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle !');
        
        $options['etude'] = $etude;
        
        if(!$response = $this->upload(false, $options))// Si tout est ok
            return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $etude->getNumero())));
        else
            return $response;
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function uploadEtudiantAction($membre_id){
        $em = $this->getDoctrine()->getManager();
        $membre = $em->getRepository('mgatePersonneBundle:Membre')->find($membre_id);

        if (!$membre)
            throw $this->createNotFoundException('Le document ne peut être lié à un membre qui n\'existe pas!');		
        
        $options['etudiant'] = $membre;
        
        if(!$response = $this->upload(false, $options))// Si tout est ok
            return $this->redirect($this->generateUrl('mgatePersonne_membre_voir', array('id' => $membre_id)));
        else
            return $response;
        
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function uploadFormationAction($id){

    } 
        
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function uploadDoctypeAction(){
        if(!$response = $this->upload(true))// Si tout est ok
            return $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));
        else
            return $response;
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        if (!$doc= $em->getRepository('mgatePubliBundle:Document')->find($id))
            throw $this->createNotFoundException('Le Document n\'existe pas !');
        
        if($doc->getRelation()){ // Cascade sucks
            $relation = $doc->getRelation()->setDocument();
            $doc->setRelation();
            $em->remove($relation);
            $em->flush(); 
        }        
        
        $em->remove($doc);
        $em->flush();        
        
        return $this->redirect($this->generateUrl('mgate_publi_documenttype_index'));  
    }

    private function upload($deleteIfExist = false, $options = array() )
    {
        $document = new Document();
        if(count($options)){
            $relatedDocument = new \mgate\PubliBundle\Entity\RelatedDocument;
            $relatedDocument->setDocument($document);
            $document->setRelation($relatedDocument);
            if(key_exists('etude', $options))
                $relatedDocument->setEtude($options['etude']);
            if(key_exists('etudiant', $options))
                $relatedDocument->setMembre($options['etudiant']);            
        }        
        
        $form = $this->createForm(new DocumentType, $document, $options);
       
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bind($this->get('request'));
               
            if( $form->isValid() )
            {
                $documentManager = $this->get('mgate.documentManager');
                $documentManager->uploadDocument($document, null, $deleteIfExist);                
                return false;
            }
        }
        return $this->render('mgatePubliBundle:Document:upload.html.twig', array('form' => $form->createView()));
    }
    

}
