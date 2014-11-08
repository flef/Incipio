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
        return array();
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