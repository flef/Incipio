<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use mgate\PersonneBundle\Entity\Personne;

/**
 * mgate\SuiviBundle\Entity\Mission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\MissionRepository")
 */
class Mission extends DocType
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="missions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;
    
    /**
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\PhaseMission", mappedBy="mission", cascade={"persist"})
     */
    private $phaseMission;

    /** , inversedBy="missions", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="\mgate\PersonneBundle\Entity\Membre", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $intervenant;

    /**
     * @var \DateTime $debutOm
     *
     * @ORM\Column(name="debutOm", type="datetime", nullable=true)
     */
    private $debutOm;

    /**
     * @var \DateTime $finOm
     *
     * @ORM\Column(name="finOm", type="datetime", nullable=true)
     */
    private $finOm;
    
    /**
     * @var integer $pourcentageJunior
     *
     * @ORM\Column(name="pourcentageJunior", type="integer", nullable=true)
     */
    private $pourcentageJunior;

    /**
     * @var integer $avancement
     *
     * @ORM\Column(name="avancement", type="integer", nullable=true)
     */
    private $avancement;

    /**
     * @var boolean $rapportDemande
     *
     * @ORM\Column(name="rapportDemande", type="boolean", nullable=true)
     */
    private $rapportDemande;

    /**
     * @var boolean $rapportRelu
     *
     * @ORM\Column(name="rapportRelu", type="boolean", nullable=true)
     */
    private $rapportRelu;

    /**
     * @var boolean $remunere
     *
     * @ORM\Column(name="remunere", type="boolean", nullable=true)
     */
    private $remunere;
    

//Block astuce pour ajout direct d'intervenant dans formulaire
    public function getMission() {
        return $this;
    }
    private $knownIntervenant = false;
    private $newIntervenant;
    
    public function isKnownIntervenant() {
        return $this->knownIntervenant;
    }
    public function setKnownIntervenant($boolean) {
        $this->knownIntervenant = $boolean;
    }
    public function getNewIntervenant() {
        return $this->newIntervenant;
    }
    public function setNewIntervenant($var) {
        $this->newIntervenant = $var;
    }
// Fin du block 

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
     * Set intervenant
     *
     * @param mgate\PersonneBundle\Entity\Membre $intervenant
     * @return Mission
     */
    public function setIntervenant(\mgate\PersonneBundle\Entity\Membre $intervenant)
    {
        $this->intervenant = $intervenant;
    
        return $this;
    }

    /**
     * Get intervenant
     *
     * @return mgate\PersonneBundle\Entity\Membre
     */
    public function getIntervenant()
    {
        return $this->intervenant;
    }

    /**
     * Set debutOm
     *
     * @param \DateTime $debutOm
     * @return Mission
     */
    public function setDebutOm($debutOm)
    {
        $this->debutOm = $debutOm;
    
        return $this;
    }

    /**
     * Get debutOm
     *
     * @return \DateTime 
     */
    public function getDebutOm()
    {
        return $this->debutOm;
    }

    /**
     * Set finOm
     *
     * @param \DateTime $finOm
     * @return Mission
     */
    public function setFinOm($finOm)
    {
        $this->finOm = $finOm;
    
        return $this;
    }

    /**
     * Get finOm
     *
     * @return \DateTime 
     */
    public function getFinOm()
    {
        return $this->finOm;
    }

    /**
     * Set avancement
     *
     * @param integer $avancement
     * @return Mission
     */
    public function setAvancement($avancement)
    {
        $this->avancement = $avancement;
    
        return $this;
    }

    /**
     * Get avancement
     *
     * @return integer 
     */
    public function getAvancement()
    {
        return $this->avancement;
    }

    /**
     * Set rapportDemande
     *
     * @param boolean $rapportDemande
     * @return Mission
     */
    public function setRapportDemande($rapportDemande)
    {
        $this->rapportDemande = $rapportDemande;
    
        return $this;
    }

    /**
     * Get rapportDemande
     *
     * @return boolean 
     */
    public function getRapportDemande()
    {
        return $this->rapportDemande;
    }

    /**
     * Set rapportRelu
     *
     * @param boolean $rapportRelu
     * @return Mission
     */
    public function setRapportRelu($rapportRelu)
    {
        $this->rapportRelu = $rapportRelu;
    
        return $this;
    }

    /**
     * Get rapportRelu
     *
     * @return boolean 
     */
    public function getRapportRelu()
    {
        return $this->rapportRelu;
    }

    /**
     * Set remunere
     *
     * @param boolean $remunere
     * @return Mission
     */
    public function setRemunere($remunere)
    {
        $this->remunere = $remunere;
    
        return $this;
    }

    /**
     * Get remunere
     *
     * @return boolean 
     */
    public function getRemunere()
    {
        return $this->remunere;
    }

    /**
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return Mission
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phaseMission = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add phaseMission
     *
     * @param \mgate\SuiviBundle\Entity\PhaseMission $phaseMission
     * @return Mission
     */
    public function addPhaseMission(\mgate\SuiviBundle\Entity\PhaseMission $phaseMission)
    {
        $this->phaseMission[] = $phaseMission;
    
        return $this;
    }

    /**
     * Remove phaseMission
     *
     * @param \mgate\SuiviBundle\Entity\PhaseMission $phaseMission
     */
    public function removePhaseMission(\mgate\SuiviBundle\Entity\PhaseMission $phaseMission)
    {
        $this->phaseMission->removeElement($phaseMission);
    }

    /**
     * Get phaseMission
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhaseMission()
    {
        return $this->phaseMission;
    }
    
    /**
     * Set pourcentageJunior
     *
     * @param integer $pourcentageJunior
     * @return Phase
     */
    public function setPourcentageJunior($pourcentageJunior) {
        $this->pourcentageJunior = $pourcentageJunior;

        return $this;
    }

    /**
     * Get pourcentageJunior
     *
     * @return integer 
     */
    public function getPourcentageJunior() {
        return $this->pourcentageJunior;
    }
    
}