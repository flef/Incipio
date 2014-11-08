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
 * RepartitionJEH
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RepartitionJEH
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission", inversedBy="repartitionsJEH") 
     */
    private $mission;
    
    /**
     * @var integer $nbrJEH
     * @ORM\Column(name="nombreJEH", type="integer", nullable=true)
     */
    private $nbrJEH;


    /**
     * @var integer $prixJEH
     * @ORM\Column(name="prixJEH", type="integer", nullable=true)
     */
    private $prixJEH;

    /**
     * @ORM\ManyToOne(targetEntity="AvMission", inversedBy="nouvelleRepartition")
     */
    private $avMission;
    
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
     * Set mission
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     * @return RepartitionJEH
     */
    public function setMission(\mgate\SuiviBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;
    
        return $this;
    }

    /**
     * Get mission
     *
     * @return \mgate\SuiviBundle\Entity\Mission 
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set nbrJEH
     *
     * @param integer $nbrJEH
     * @return RepartitionJEH
     */
    public function setNbrJEH($nbrJEH)
    {
        $this->nbrJEH = $nbrJEH;
    
        return $this;
    }

    /**
     * Get nbrJEH
     *
     * @return integer 
     */
    public function getNbrJEH()
    {
        return $this->nbrJEH;
    }

    /**
     * Set prixJEH
     *
     * @param integer $prixJEH
     * @return RepartitionJEH
     */
    public function setPrixJEH($prixJEH)
    {
        $this->prixJEH = $prixJEH;
    
        return $this;
    }

    /**
     * Get prixJEH
     *
     * @return integer 
     */
    public function getPrixJEH()
    {
        return $this->prixJEH;
    }
    
    /**
     * Set avMission
     *
     * @param \mgate\SuiviBundle\Entity\AvMission $avenant
     * @return RepartitionJEH
     */
    public function setAvMission(\mgate\SuiviBundle\Entity\AvMission $avMission = null)
    {
        $this->avMission = $avMission;
    
        return $this;
    }

    /**
     * Get avMission
     *
     * @return \mgate\SuiviBundle\Entity\AvMission
     */
    public function getAvMission()
    {
        return $this->avMission;
    }
}