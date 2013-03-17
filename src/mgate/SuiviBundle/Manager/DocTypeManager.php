<?php

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Manager\BaseManager;
use mgate\SuiviBundle\Entity\DocType as DocType;
use mgate\PersonneBundle\Entity\Employe as Employe;

class DocTypeManager /*extends \Twig_Extension*/
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    // Pour utiliser les fonctions depuis twig
    public function getName()
    {
        return 'mgate_DocTypeManager';
    }
    // Pour utiliser les fonctions depuis twig
    public function getFunctions()
    {
        return array(
            //'getRefEtude' => new \Twig_Function_Method($this, 'getRefEtude')
        );
    }
    
    public function getRepository()
    {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }

    
    public function checkSaveNewEmploye($doc)
    {
        if(!$doc->isKnownSignataire2())
        {
            $doc->setSignataire2($doc->getNewSignataire2()->getPersonne());

            $doc->getNewSignataire2()->setProspect($doc->getEtude()->getProspect());
            $this->em->persist($doc->getNewSignataire2());
        }

        
    }
    
}