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
use mgate\SuiviBundle\Entity\DocType as DocType;

/**
 * NoteDeFrais
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"mandat", "numero"})})
 * @ORM\Entity(repositoryClass="mgate\TresoBundle\Entity\NoteDeFraisRepository")
 */
class NoteDeFrais
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
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="date",nullable=false)
     */
    private $date;
    
    /**
     * @var integer $mandat
     *
     * @ORM\Column(name="mandat", type="integer", nullable=false)
     */
    private $mandat;

    /**
     * @var integer $num
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero;
    
    /**
     * @ORM\Column(name="objet", type="text", nullable=false)
     * @var string
     */
    private $objet;
    
    /**
     * @ORM\OneToMany(targetEntity="NoteDeFraisDetail", mappedBy="noteDeFrais", cascade={"persist", "detach", "remove"}, orphanRemoval=true)
     */
    private $details;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $demandeur;


    /**
     * 
     * ADDITIONAL GETTERS
     */
    public function getMontantHT(){
       $montantHT = 0;
       foreach ($this->details as $detail){
            $montantHT += $detail->getMontantHT();           
       }
       return $montantHT;
    }
    
    public function getMontantTVA(){
        $TVA = 0;
        foreach ($this->details as $detail){
            $TVA += $detail->getMontantTVA();
       }
       return $TVA;
    }
    
    public function getMontantTTC(){
        return $this->getMontantHT() + $this->getMontantTVA();
    }
    
    public function getReference(){
        // UNSAFE
        return $this->mandat.'-NF'.$this->getNumero().'-'.$this->getDemandeur()->getMembre()->getIdentifiant();
    }


    /*
     * STANDARDS GETTERS/SETTERS
     */
    
    
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
        $this->details = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set objet
     *
     * @param string $objet
     * @return NoteDeFrais
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;
    
        return $this;
    }

    /**
     * Get objet
     *
     * @return string 
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Add details
     *
     * @param \mgate\TresoBundle\Entity\NoteDeFraisDetail $details
     * @return NoteDeFrais
     */
    public function addDetail(\mgate\TresoBundle\Entity\NoteDeFraisDetail $details)
    {
        $this->details[] = $details;
    
        return $this;
    }

    /**
     * Remove details
     *
     * @param \mgate\TresoBundle\Entity\NoteDeFraisDetail $details
     */
    public function removeDetail(\mgate\TresoBundle\Entity\NoteDeFraisDetail $details)
    {
        $this->details->removeElement($details);
        $details->setNoteDeFrais();
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set demandeur
     *
     * @param \mgate\PersonneBundle\Entity\Personne $demandeur
     * @return NoteDeFrais
     */
    public function setDemandeur(\mgate\PersonneBundle\Entity\Personne $demandeur = null)
    {
        $this->demandeur = $demandeur;
    
        return $this;
    }

    /**
     * Get demandeur
     *
     * @return \mgate\PersonneBundle\Entity\Personne 
     */
    public function getDemandeur()
    {
        return $this->demandeur;
    }

    /**
     * Set mandat
     *
     * @param integer $mandat
     * @return NoteDeFrais
     */
    public function setMandat($mandat)
    {
        $this->mandat = $mandat;
    
        return $this;
    }

    /**
     * Get mandat
     *
     * @return integer 
     */
    public function getMandat()
    {
        return $this->mandat;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return NoteDeFrais
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    
        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return NoteDeFrais
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
