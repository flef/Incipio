<?php

namespace mgate\PubliBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use mgate\PubliBundle\Manager\BaseManager;
use mgate\PubliBundle\Entity\Document;
use mgate\PubliBundle\Entity\RelatedDocument;
 

class DocumentManager extends BaseManager{
    protected $em;
    protected $securityContext;
    protected $container;

    public function __construct(EntityManager $em, Container $container, SecurityContext $securityContext){
        $this->em = $em;
        $this->container = $container;
        $this->securityContext = $securityContext;
    }
    
    public function uploadDocumentFromUrl($url, array $authorizedMIMEType, $name, $relatedDocument = null, $deleteIfExist = false){
        $tempStorage = 'tmp'.sha1(uniqid(mt_rand(), true));
        
        if(($handle = @fopen($url , 'r')) === FALSE) // Erreur
            throw new \Exception('La ressource demandée ne peut être lue.');

        file_put_contents($tempStorage, $handle);
        fclose($handle);
        // MIME-type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tempStorage);
        $extension = substr(strrchr($mimi, "\\"), 1);

        // le dernier true indique de ne pas vérifier si le fichier à été téléchargé en HTTP
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile($tempStorage, $name.'.'.$extension, $mime, filesize($tempStorage), null, true);
        
        return $this->uploadDocumentFromFile($file, $authorizedMIMEType, $name, $relatedDocument, $deleteIfExist);
    }
    
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
    
    public function uploadDocument(Document $document, RelatedDocument $relatedDocument = null, $deleteIfExist = false){
        // Relations
        if($relatedDocument){
            $document->setRelation($relatedDocument);
            $relatedDocument->setDocument($document);
        }
        
        // Subdirectory
        $junior = $this->container->getParameter('junior');
        if(!array_key_exists('id', $junior))
            throw new \Exception('Votre version de Incipio est obsolète. Contactez support@incipio.fr (incorrect parameters junior : id)');
        $juniorId = $junior['id'];
        
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


    public function getRepository(){
        return $this->em->getRepository('mgatePubliBundle:Document');
    }

}