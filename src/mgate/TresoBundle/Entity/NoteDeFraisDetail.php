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
 * NoteDeFraisDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class NoteDeFraisDetail
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
     * @ORM\ManyToOne(targetEntity="NoteDeFrais", inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $noteDeFrais;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="prixHT", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $prixHT;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxTVA", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $tauxTVA;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="kilometrage", type="integer", nullable=true)
     */
    private $kilometrage;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxKm", type="integer", nullable=true)
     */
    private $tauxKm;
    
    /**
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumn(nullable=true)
     */
    private $compte;

    
    //categorie à ajouter via ManytoMany compteComptable
    
    // Perso
    public static function getTypeChoices(){
        return array(1 => 'Classique',
            2 => 'Kilométrique',);
    } 
    
    
    public function getMontantHT(){        
        if($this->type == 1)
            return $this->prixHT;
        else
            return $this->kilometrage * $this->tauxKm / 100;
    }
    
    public function getMontantTVA(){
        if($this->type == 1)
            return $this->tauxTVA * $this->getMontantHT() / 100;
        else
            return 0;            
    }
    
    public function getMontantTTC(){
        return $this->getMontantHT() + $this->getMontantTVA();
    }












    // Getter Setter Auto Generated

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
     * @return NoteDeFraisDetail
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
     * Set prixHT
     *
     * @param float $prixHT
     * @return NoteDeFraisDetail
     */
    public function setPrixHT($prixHT)
    {
        $this->prixHT = $prixHT;
    
        return $this;
    }

    /**
     * Get prixHT
     *
     * @return float 
     */
    public function getPrixHT()
    {
        return $this->prixHT;
    }

    /**
     * Set tauxTVA
     *
     * @param float $tauxTVA
     * @return NoteDeFraisDetail
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
     * Set type
     *
     * @param integer $type
     * @return NoteDeFraisDetail
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set kilometrage
     *
     * @param integer $kilometrage
     * @return NoteDeFraisDetail
     */
    public function setKilometrage($kilometrage)
    {
        $this->kilometrage = $kilometrage;
    
        return $this;
    }

    /**
     * Get kilometrage
     *
     * @return integer 
     */
    public function getKilometrage()
    {
        return $this->kilometrage;
    }

    /**
     * Set tauxKm
     *
     * @param float $tauxKm
     * @return NoteDeFraisDetail
     */
    public function setTauxKm($tauxKm)
    {
        $this->tauxKm = $tauxKm;
    
        return $this;
    }

    /**
     * Get tauxKm
     *
     * @return float 
     */
    public function getTauxKm()
    {
        return $this->tauxKm;
    }

    /**
     * Set categorie
     *
     * @param integer $categorie
     * @return NoteDeFraisDetail
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    
        return $this;
    }

    /**
     * Get categorie
     *
     * @return integer 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set noteDeFrais
     *
     * @param \mgate\TresoBundle\Entity\NoteDeFrais $noteDeFrais
     * @return NoteDeFraisDetail
     */
    public function setNoteDeFrais(\mgate\TresoBundle\Entity\NoteDeFrais $noteDeFrais = null)
    {
        $this->noteDeFrais = $noteDeFrais;
    
        return $this;
    }

    /**
     * Get noteDeFrais
     *
     * @return \mgate\TresoBundle\Entity\NoteDeFrais 
     */
    public function getNoteDeFrais()
    {
        return $this->noteDeFrais;
    }

    /**
     * Set compte
     *
     * @param \mgate\TresoBundle\Entity\Compte $compte
     * @return NoteDeFraisDetail
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
}