<?php

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
     * @ORM\OneToMany(targetEntity="NoteDeFraisDetail", mappedBy="noteDeFrais", cascade={"persist", "merge", "refresh", "remove"})
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
           if($detail->getType() == 1)
               $TVA += $detail->getPrixHT() * $detail->getTauxTVA() / 100;
       }
       return $TVA;
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