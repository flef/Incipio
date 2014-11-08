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
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\Ap
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ap extends DocType
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
     * @ORM\OneToOne(targetEntity="Etude", inversedBy="ap")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    protected $etude;
    
    /** nombre de developpeur estimé
     * @var integer $nbrDev
     *
     * @ORM\Column(name="nbrDev", type="integer", nullable=true)
     */
    private $nbrDev;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $contactMgate;
    
    /**
     * @var boolean $deonto
     *
     * @ORM\Column(name="deonto", type="boolean", nullable=true)
     */
    private $deonto;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getReference()
    {
        return $this->etude->getReference().'-AP-'.$this->getVersion();
    }
        
    /**
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return Ap
     */
    public function setEtude(\mgate\SuiviBundle\Entity\Etude $etude = null)
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
     * Set nbrDev
     *
     * @param integer $nbrDev
     * @return Ap
     */
    public function setNbrDev($nbrDev)
    {
        $this->nbrDev = $nbrDev;
    
        return $this;
    }

    /**
     * Get nbrDev
     *
     * @return integer 
     */
    public function getNbrDev()
    {
        return $this->nbrDev;
    }
    
    /**
     * Set contactMgate
     *
     * @param \mgate\PersonneBundle\Entity\Personne $contactMgate
     * @return Ap
     */
    public function setContactMgate(\mgate\PersonneBundle\Entity\Personne $contactMgate = null) {
        $this->contactMgate = $contactMgate;

        return $this;
    }

    /**
     * Get contactMgate
     *
     * @return \mgate\PersonneBundle\Entity\Personne
     */
    public function getContactMgate() {
        return $this->contactMgate;
    }
    
    /**
     * Set deonto
     *
     * @param boolean $deonto
     * @return Ap
     */
    public function setDeonto($deonto) {
        $this->deonto = $deonto;

        return $this;
    }

    /**
     * Get deonto
     *
     * @return boolean 
     */
    public function getDeonto() {
        return $this->deonto;
    }
}