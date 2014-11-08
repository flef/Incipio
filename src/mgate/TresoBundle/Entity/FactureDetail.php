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
 * FactureDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FactureDetail
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
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $facture;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="montantHT", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $montantHT;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxTVA", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $tauxTVA;

    /**
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumn(nullable=true)
     */
    private $compte;
    
    /**
     * ADDITIONAL
     */
    
    public function getMontantTVA(){
        return $this->tauxTVA * $this->montantHT / 100;        
    }
    
    public function getMontantTTC(){
        return $this->montantHT + $this->getMontantTVA();
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
     * Set description
     *
     * @param string $description
     * @return FactureDetail
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set montantHT
     *
     * @param float $montantHT
     * @return FactureDetail
     */
    public function setMontantHT($montantHT)
    {
        $this->montantHT = $montantHT;
    
        return $this;
    }

    /**
     * Get montantHT
     *
     * @return float 
     */
    public function getMontantHT()
    {
        return $this->montantHT;
    }

    /**
     * Set tauxTVA
     *
     * @param float $tauxTVA
     * @return FactureDetail
     */
    public function setTauxTVA($tauxTVA)
    {
        $this->tauxTVA = $tauxTVA;
    
        return $this;
    }

    /**
     * Get tauxTVA
     *
     * @return float 
     */
    public function getTauxTVA()
    {
        return $this->tauxTVA;
    }

    /**
     * Set compte
     *
     * @param \mgate\TresoBundle\Entity\Compte $compte
     * @return FactureDetail
     */
    public function setCompte(\mgate\TresoBundle\Entity\Compte $compte = null)
    {
        $this->compte = $compte;
    
        return $this;
    }

    /**
     * Get compte
     *
     * @return \mgate\TresoBundle\Entity\Compte 
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set facture
     *
     * @param \mgate\TresoBundle\Entity\Facture $facture
     * @return FactureDetail
     */
    public function setFacture(\mgate\TresoBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;
    
        return $this;
    }

    /**
     * Get facture
     *
     * @return \mgate\TresoBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }
}