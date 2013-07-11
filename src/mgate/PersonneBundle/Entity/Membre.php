<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\PersonneBundle\Entity\Membre
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\MembreRepository")
 */
class Membre
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;  
    
    /**
     * @ORM\OneToOne(targetEntity="Personne", inversedBy="membre", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $personne;
    
    /**
     * @var string $identifiant
     *
     * @ORM\Column(name="identifiant", type="string", length=10, nullable=true, unique=true)
     */
    private $identifiant;
    
    /**
     * @var int $promotion
     * @ORM\Column(name="promotion", type="smallint", nullable=true)
     */
    private $promotion;
    
    /**
     * @var date $datedDeNaissance
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     */
    private $dateDeNaissance;
    
    /**
     * @var string $lieuDeNaissancce
     * @ORM\Column(name="placeofbirth", type="string", nullable=true)
     */
    private $lieuDeNaissance;
   

    /**
     * @ORM\OneToMany(targetEntity="mgate\PersonneBundle\Entity\Mandat", mappedBy="membre", cascade={"persist","remove"})
     */
    private $mandats;

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
     * Set identifiant
     *
     * @param string $identifiant
     * @return Membre
     */
    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;
    
        return $this;
    }

    /**
     * Get identifiant
     *
     * @return string 
     */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    /**
     * Set personne
     *
     * @param \mgate\PersonneBundle\Entity\Personne $personne
     * @return Membre
     */
    public function setPersonne(\mgate\PersonneBundle\Entity\Personne $personne=null)
    {
        if($personne!=null)
        $personne->setMembre($this);
        $this->personne = $personne;
    
        return $this;
    }

    /**
     * Get personne
     *
     * @return \mgate\PersonneBundle\Entity\Personne 
     */
    public function getPersonne()
    {
        return $this->personne;
    }
    
    /**
     * Set poste
     *
     * @param \mgate\PersonneBundle\Entity\Membre $poste
     * @return Membre
     */
    public function setPoste(\mgate\PersonneBundle\Entity\Poste $poste=null)
    {
        $this->poste = $poste;
    
        return $this;
    }

    /**
     * Get poste
     *
     * @return \mgate\PersonneBundle\Entity\Membre
     */
    public function getPoste()
    {
        return $this->poste;
    }
 
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mandats = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     * @return Membre
     */
    public function addMandat(\mgate\PersonneBundle\Entity\Mandat $mandats)
    {
        $this->mandats[] = $mandats;
    
        return $this;
    }

    /**
     * Remove mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     */
    public function removeMandat(\mgate\PersonneBundle\Entity\Mandat $mandats)
    {
        $this->mandats->removeElement($mandats);
    }

    /**
     * Get mandats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandats()
    {
        return $this->mandats;
    }

    /**
     * Set promotion
     *
     * @param integer $promotion
     * @return Membre
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    
        return $this;
    }

    /**
     * Get promotion
     *
     * @return integer 
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Set dateDeNaissance
     *
     * @param \DateTime $dateDeNaissance
     * @return Membre
     */
    public function setDateDeNaissance($dateDeNaissance)
    {
        $this->dateDeNaissance = $dateDeNaissance;
    
        return $this;
    }

    /**
     * Get dateDeNaissance
     *
     * @return \DateTime 
     */
    public function getDateDeNaissance()
    {
        return $this->dateDeNaissance;
    }

    /**
     * Set lieuDeNaissance
     *
     * @param string $lieuDeNaissance
     * @return Membre
     */
    public function setLieuDeNaissance($lieuDeNaissance)
    {
        $this->lieuDeNaissance = $lieuDeNaissance;
    
        return $this;
    }

    /**
     * Get lieuDeNaissance
     *
     * @return string 
     */
    public function getLieuDeNaissance()
    {
        return $this->lieuDeNaissance;
    }
}