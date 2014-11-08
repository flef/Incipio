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

/**
 * @brief Document Manager
 * @author Florian Lefèvre
 * @date 21 avril 2014
 * @copyright (c) 2014, Florian Lefèvre
 *
 * Manager pour l'upload de Documents (Aucun document ne doit être persisté sans utiliser ces méthodes)
 *
 */
namespace mgate\PubliBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use mgate\PubliBundle\Manager\BaseManager;
use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Entity\RelatedDocument;
 
/**
 * 
 */
class DocumentManager extends BaseManager{
    protected $em;
    protected $securityContext;
    protected $container;

    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    public function __construct(EntityManager $em, Container $container, SecurityContext $securityContext){
        $this->em = $em;
        $this->container = $container;
        $this->securityContext = $securityContext;
    }
    
    /**
     * Upload un document sur le serveur depuis une ressource distante via HTTP
     * @param string $url
     * @param array $authorizedMIMEType
     * @param string $name
     * @param string $relatedDocument
     * @param string $deleteIfExist
     * @return \mgate\PubliBundle\Entity\Document
     * @throws \Exception
     */
    public function uploadDocumentFromUrl($url, array $authorizedMIMEType, $name, $relatedDocument = null, $deleteIfExist = false){
        $tempStorage = 'tmp/'.sha1(uniqid(mt_rand(), true));
        
        if(($handle = @fopen($url , 'r')) === FALSE) // Erreur
            throw new \Exception('La ressource demandée ne peut être lue.');

        file_put_contents($tempStorage, $handle);
        fclose($handle);
        // MIME-type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tempStorage);
        $extension = substr(strrchr($mime, "\\"), 1);

        // le dernier true indique de ne pas vérifier si le fichier à été téléchargé en HTTP
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile($tempStorage, $name.'.'.$extension, $mime, filesize($tempStorage), null, true);
        
        return $this->uploadDocumentFromFile($file, $authorizedMIMEType, $name, $relatedDocument, $deleteIfExist);
    }
    
    /**
     * Upload un fichier de type UploadedFile sur le serveur
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param array $authorizedMIMEType
     * @param string $name
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocument
     * @param boolean $deleteIfExist
     * @return \mgate\PubliBundle\Entity\Document
     * @throws \Exception
     */
    public function uploadDocumentFromFile(UploadedFile $file, array $authorizedMIMEType, $name, RelatedDocument $relatedDocument = null, $deleteIfExist = false){
        $document = new Document; 
        
        // MIME-type Check
        if(!in_array($file->getMimeType(), $authorizedMIMEType)) // Erreur
            throw new \Exception('Le type de fichier n\'est pas autorisé.');
        
        // Author
        $user = $this->securityContext->getToken()->getUser();
        $personne = $user->getPersonne();
        $document->setAuthor($personne);
    
        // File
        $document->setFile($file);
        $document->setName($name);
        
        return $this->uploadDocument($document, $relatedDocument, $deleteIfExist);
    }
    
    /**
     * uploadDocument has to be the only one fonction used to persist Document
     * @param \mgate\PubliBundle\Entity\RelatedDocument $document
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocument
     * @param boolean $deleteIfExist
     * @return \mgate\PubliBundle\Entity\Document
     * @throws \Exception
     * @throws \Symfony\Component\HttpFoundation\File\Exception\UploadException
     */
    public function uploadDocument(Document $document, RelatedDocument $relatedDocument = null, $deleteIfExist = false){
        // Relations
        if($relatedDocument){
            $document->setRelation($relatedDocument);
            $relatedDocument->setDocument($document);
        }
        
        // Store each Junior documents in a distinct subdirectory
        $junior = $this->container->getParameter('junior');
        if(!array_key_exists('id', $junior))
            throw new \Exception('Votre version de Incipio est obsolète. Contactez support@incipio.fr (incorrect parameters junior : id)');
        $juniorId = $junior['id'];
        $document->setSubdirectory($juniorId);
        
        // Authorized Storage Size Overflow
        $totalSize = $document->getSize() + $this->getRepository()->getTotalSize();
        if($totalSize > $junior['authorizedStorageSize'])
            throw new \Symfony\Component\HttpFoundation\File\Exception\UploadException('Vous n\'avez plus d\'espace disponible ! Vous pouvez en demander plus à contact@incipio.fr.');

        
        // Delete every document with the same name
        if($deleteIfExist){
            $docs = $this->getRepository()->findBy(array('name' => $document->getName() ));
            foreach ($docs as $doc) {
                if($doc->getRelation()){
                    $relation = $doc->getRelation();
                    $doc->setRelation();
                    $this->em->remove ($relation);
                    $this->em->flush();
                }
                $this->em->remove($doc);
            }
        }
        $this->persistAndFlush($document);
        return $document;
    } 

    /**
     * 
     * @return \mgate\PubliBundle\Entity\DocumentRepository
     */
    public function getRepository(){
        return $this->em->getRepository('mgatePubliBundle:Document');
    }

}