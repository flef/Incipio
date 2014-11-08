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


namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\ProcesVerbal
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProcesVerbal extends DocType
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="procesVerbaux", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;
    

    //Justification du choix: choix des phases dans un select multiple destiner qu'à un affichage, aucun traitement sur les phases
    /**
     * @var string $type
     *
     * @ORM\Column(name="phaseIDs", type="integer", nullable=true)
     */
    protected $phaseID;
    
    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="text", nullable=true)
     */
    private $type;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return ProcesVerbal
     */
    public function setEtude(\mgate\SuiviBundle\Entity\Etude $etude)
    {
        $this->etude = $etude;
    
        return $this;
    }

    /**
     * Get etude
     *
     * @return mgate\SuiviBundle\Entity\Etude 
     */
    public function getEtude()
    {
        return $this->etude;
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return ProcesVerbal
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set phaseIDs
     *
     * @param array $phaseIDs
     * @return ProcesVerbal
     */
    public function setPhaseIDs($phaseIDs)
    {
        $this->phaseIDs = $phaseIDs;
    
        return $this;
    }

    /**
     * Get phaseIDs
     *
     * @return array 
     */
    public function getPhaseIDs()
    {
        return $this->phaseIDs;
    }

    /**
     * Set phaseID
     *
     * @param integer $phaseID
     * @return ProcesVerbal
     */
    public function setPhaseID($phaseID)
    {
        $this->phaseID = $phaseID;
    
        return $this;
    }

    /**
     * Get phaseID
     *
     * @return integer 
     */
    public function getPhaseID()
    {
        return $this->phaseID;
    }
}