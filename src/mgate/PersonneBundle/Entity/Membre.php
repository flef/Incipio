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
class Membre {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
       
    /**
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\Mission", mappedBy="intervenant", cascade={"persist","remove"})
     */
    private $missions;

    /**
     * @ORM\OneToOne(targetEntity="mgate\PersonneBundle\Entity\Personne", inversedBy="membre", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $personne;
    
    /**
     * @var \Date $dateSignature
     *
     * @ORM\Column(name="dateCE", type="date",nullable=true)
     */
    private $dateConventionEleve;

    /**
     * @var string $identifiant
     *
     * @ORM\Column(name="identifiant", type="string", length=10, nullable=true, unique=true)
     */
    private $identifiant;
    
    /**
     * @var string $emailEMSE
     *
     * @ORM\Column(name="emailEMSE", type="string", length=50, nullable=true)
     */
    private $emailEMSE;

    /**
     * @var int $promotion
     * @ORM\Column(name="promotion", type="smallint", nullable=true)
     */
    private $promotion;
    
    /**
     * @var int $appartement
     * @ORM\Column(name="appartement", type="smallint", nullable=true)
     */
    private $appartement;

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
     * @var string $nationalite
     * @ORM\Column(name="nationalite", type="string", nullable=true)
     */
    private $nationalite;
    
    /**
     * @ORM\OneToMany(targetEntity="mgate\PubliBundle\Entity\RelatedDocument", mappedBy="membre", cascade={"remove"})
     */
    private $relatedDocuments;    
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set identifiant
     *
     * @param string $identifiant
     * @return Membre
     */
    public function setIdentifiant($identifiant) {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * Get identifiant
     *
     * @return string 
     */
    public function getIdentifiant() {
        return $this->identifiant;
    }

    /**
     * Set personne
     *
     * @param \mgate\PersonneBundle\Entity\Personne $personne
     * @return Membre
     */
    public function setPersonne(\mgate\PersonneBundle\Entity\Personne $personne = null) {
        if ($personne != null)
            $personne->setMembre($this);
        $this->personne = $personne;

        return $this;
    }

    /**
     * Get personne
     *
     * @return \mgate\PersonneBundle\Entity\Personne 
     */
    public function getPersonne() {
        return $this->personne;
    }

    /**
     * Set poste
     *
     * @param \mgate\PersonneBundle\Entity\Membre $poste
     * @return Membre
     */
    public function setPoste(\mgate\PersonneBundle\Entity\Poste $poste = null) {
        $this->poste = $poste;
        return $this;
    }

    /**
     * Get poste
     *
     * @return \mgate\PersonneBundle\Entity\Membre
     */
    public function getPoste() {
        return $this->poste;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->mandats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->missions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relatedDocuments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     * @return Membre
     */
    public function addMandat(\mgate\PersonneBundle\Entity\Mandat $mandats) {
        $this->mandats[] = $mandats;

        return $this;
    }

    /**
     * Remove mandats
     *
     * @param \mgate\PersonneBundle\Entity\Mandat $mandats
     */
    public function removeMandat(\mgate\PersonneBundle\Entity\Mandat $mandats) {
        $this->mandats->removeElement($mandats);
    }

    /**
     * Get mandats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandats() {
        return $this->mandats;
    }

    /**
     * Set promotion
     *
     * @param integer $promotion
     * @return Membre
     */
    public function setPromotion($promotion) {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return integer 
     */
    public function getPromotion() {
        return $this->promotion;
    }

    /**
     * Set dateDeNaissance
     *
     * @param \DateTime $dateDeNaissance
     * @return Membre
     */
    public function setDateDeNaissance($dateDeNaissance) {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * Get dateDeNaissance
     *
     * @return \DateTime 
     */
    public function getDateDeNaissance() {
        return $this->dateDeNaissance;
    }

    /**
     * Set lieuDeNaissance
     *
     * @param string $lieuDeNaissance
     * @return Membre
     */
    public function setLieuDeNaissance($lieuDeNaissance) {
        $this->lieuDeNaissance = $lieuDeNaissance;

        return $this;
    }

    /**
     * Get lieuDeNaissance
     *
     * @return string 
     */
    public function getLieuDeNaissance() {
        return $this->lieuDeNaissance;
    }


    /**
     * Set appartement
     *
     * @param integer $appartement
     * @return Membre
     */
    public function setAppartement($appartement)
    {
        $this->appartement = $appartement;
    
        return $this;
    }

    /**
     * Get appartement
     *
     * @return integer 
     */
    public function getAppartement()
    {
        return $this->appartement;
    }
	
	 /**
     * Set nationalite
     *
     * @param string $nationalite
     * @return Membre
     */
    public function setNationalite($nationalite)
    {
        $this->nationalite = $nationalite;
    
        return $this;
    }

    /**
     * Get nationalite
     *
     * @return string 
     */
    public function getNationalite()
    {
        return $this->nationalite;
    }
    
    /**
     * Set emailEMSE
     *
     * @param string $emailEMSE
     * @return Membre
     */
    public function setEmailEMSE($emailEMSE) {
        $this->emailEMSE = $emailEMSE;

        return $this;
    }

    /**
     * Get emailEMSE
     *
     * @return string 
     */
    public function getEmailEMSE() {
        return $this->emailEMSE;
    }

    /**
     * Set dateConventionEleve
     *
     * @param \DateTime $dateConventionEleve
     * @return Membre
     */
    public function setDateConventionEleve($dateConventionEleve)
    {
        $this->dateConventionEleve = $dateConventionEleve;
    
        return $this;
    }

    /**
     * Get dateConventionEleve
     *
     * @return \DateTime 
     */
    public function getDateConventionEleve()
    {
        return $this->dateConventionEleve;
    }

    /**
     * Add missions
     *
     * @param \mgate\SuiviBundle\Entity\Mission $missions
     * @return Membre
     */
    public function addMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions[] = $missions;
    
        return $this;
    }

    /**
     * Remove missions
     *
     * @param \mgate\SuiviBundle\Entity\Mission $missions
     */
    public function removeMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions->removeElement($missions);
    }

    /**
     * Get missions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * Add relatedDocuments
     *
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments
     * @return Membre
     */
    public function addRelatedDocument(\mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments[] = $relatedDocuments;
    
        return $this;
    }

    /**
     * Remove relatedDocuments
     *
     * @param \mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments
     */
    public function removeRelatedDocument(\mgate\PubliBundle\Entity\RelatedDocument $relatedDocuments)
    {
        $this->relatedDocuments->removeElement($relatedDocuments);
    }

    /**
     * Get relatedDocuments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelatedDocuments()
    {
        return $this->relatedDocuments;
    }
}