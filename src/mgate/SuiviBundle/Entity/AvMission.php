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
 * mgate\SuiviBundle\Entity\AvMission
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AvMission extends DocType
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="avMissions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;
    
    /**
     * @var Mission
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission")
     */
    private $mission;
    
    /**
     * @var integer $nouvelleRepartition
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\RepartitionJEH", mappedBy="avMission", cascade={"persist","remove"})
     */
    private $nouvelleRepartition;
    
    /**
     * @var interger $nouveauPourcentage
     * @ORM\Column(name="nouveauPourcentage", type="integer")
     */
    private $nouveauPourcentage;

    /**
     * @var integer $differentielDelai
     * @ORM\Column(name="differentielDelai", type="integer")
     */
    private $differentielDelai;
    
    /**
     * @ORM\ManyToOne(targetEntity="Av", inversedBy="avenantsMissions")
     */
    private $avenant;

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
     * Constructor
     */
    public function __construct()
    {
        $this->nouvelleRepartition = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set differentielDelai
     *
     * @param integer $differentielDelai
     * @return AvMission
     */
    public function setDifferentielDelai($differentielDelai)
    {
        $this->differentielDelai = $differentielDelai;
    
        return $this;
    }

    /**
     * Get differentielDelai
     *
     * @return integer 
     */
    public function getDifferentielDelai()
    {
        return $this->differentielDelai;
    }

    /**
     * Add nouvelleRepartition
     *
     * @param \mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition
     * @return AvMission
     */
    public function addNouvelleRepartition(\mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition)
    {
        $this->nouvelleRepartition[] = $nouvelleRepartition;
    
        return $this;
    }

    /**
     * Remove nouvelleRepartition
     *
     * @param \mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition
     */
    public function removeNouvelleRepartition(\mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition)
    {
        $this->nouvelleRepartition->removeElement($nouvelleRepartition);
    }

    /**
     * Get nouvelleRepartition
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNouvelleRepartition()
    {
        return $this->nouvelleRepartition;
    }

    /**
     * Set mission
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     * @return AvMission
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
     * Set nouveauPourcentage
     *
     * @param integer $nouveauPourcentage
     * @return AvMission
     */
    public function setNouveauPourcentage($nouveauPourcentage)
    {
        $this->nouveauPourcentage = $nouveauPourcentage;
    
        return $this;
    }

    /**
     * Get nouveauPourcentage
     *
     * @return integer 
     */
    public function getNouveauPourcentage()
    {
        return $this->nouveauPourcentage;
    }
    
    /**
     * Set avenant
     *
     * @param \mgate\SuiviBundle\Entity\Av $avenant
     * @return AvMission
     */
    public function setAvenant(\mgate\SuiviBundle\Entity\Av $avenant = null)
    {
        $this->avenant = $avenant;
    
        return $this;
    }

    /**
     * Get avenant
     *
     * @return \mgate\SuiviBundle\Entity\Av 
     */
    public function getAvenant()
    {
        return $this->avenant;
    }
    
    
    /**
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return AvMission
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
}