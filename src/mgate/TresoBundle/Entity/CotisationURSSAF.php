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


namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CotisationURSSAF
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\TresoBundle\Entity\CotisationURSSAFRepository")
 */
class CotisationURSSAF
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
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSurBaseURSSAF", type="boolean")
     */
    private $isSurBaseURSSAF;

    /**
     * @var string
     *
     * @ORM\Column(name="tauxPartJE", type="decimal", scale=4)
     */
    private $tauxPartJE;

    /**
     * @var string
     *
     * @ORM\Column(name="tauxPartEtu", type="decimal", scale=4)
     */
    private $tauxPartEtu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date")
     */
    private $dateFin;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="deductible", type="boolean")
     */
    private $deductible;
    
    
    public function __construct() {
        $this->deductible = true;
        return $this;
    }

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
     * Set libelle
     *
     * @param string $libelle
     * @return CotisationURSSAF
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set isSurBaseURSSAF
     *
     * @param boolean $isSurBaseURSSAF
     * @return CotisationURSSAF
     */
    public function setIsSurBaseURSSAF($isSurBaseURSSAF)
    {
        $this->isSurBaseURSSAF = $isSurBaseURSSAF;
    
        return $this;
    }

    /**
     * Get isSurBaseURSSAF
     *
     * @return boolean 
     */
    public function getIsSurBaseURSSAF()
    {
        return $this->isSurBaseURSSAF;
    }

    /**
     * Set tauxPartJE
     *
     * @param string $tauxPartJE
     * @return CotisationURSSAF
     */
    public function setTauxPartJE($tauxPartJE)
    {
        $this->tauxPartJE = $tauxPartJE;
    
        return $this;
    }

    /**
     * Get tauxPartJE
     *
     * @return string 
     */
    public function getTauxPartJE()
    {
        return $this->tauxPartJE;
    }

    /**
     * Set tauxPartEtu
     *
     * @param string $tauxPartEtu
     * @return CotisationURSSAF
     */
    public function setTauxPartEtu($tauxPartEtu)
    {
        $this->tauxPartEtu = $tauxPartEtu;
    
        return $this;
    }

    /**
     * Get tauxPartEtu
     *
     * @return string 
     */
    public function getTauxPartEtu()
    {
        return $this->tauxPartEtu;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return CotisationURSSAF
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    
        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return CotisationURSSAF
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
    
        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set deductible
     *
     * @param boolean $deductible
     * @return CotisationURSSAF
     */
    public function setDeductible($deductible)
    {
        $this->deductible = $deductible;
    
        return $this;
    }

    /**
     * Get deductible
     *
     * @return boolean 
     */
    public function getDeductible()
    {
        return $this->deductible;
    }
}