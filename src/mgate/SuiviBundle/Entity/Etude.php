<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use mgate\PersonneBundle\Entity\Client;
use mgate\PersonneBundle\Entity\User;

/**
 * mgate\SuiviBundle\Entity\Etude
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\EtudeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Etude
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** inversedBy="etudes", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Prospect")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $prospect;
    
    /** inversedBy="etudes", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $suiveur;

    /**
     * @var \DateTime $dateCreation
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var integer $mandat
     *
     * @ORM\Column(name="mandat", type="integer")
     */
    private $mandat;

    /**
     * @var integer $num
     *
     * @ORM\Column(name="num", type="integer", nullable=true)
     */
    private $num;

    /**
     * @var boolean $dossierCree
     *
     * @ORM\Column(name="dossierCree", type="boolean", nullable=true)
     */
    private $dossierCree;
    
    /**
     * @var string $nom
     *
     * @ORM\Column(name="nom", type="text", nullable=false)
     */
    private $nom;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string $competences
     *
     * @ORM\Column(name="competences", type="text", nullable=true)
     */
    private $competences;

    /**
     * @var boolean $deonto
     *
     * @ORM\Column(name="deonto", type="boolean", nullable=true)
     */
    private $deonto;

    /**
     * @var boolean $mailEntretienEnvoye
     *
     * @ORM\Column(name="mailEntretienEnvoye", type="boolean", nullable=true)
     */
    private $mailEntretienEnvoye;

    /**
     * @var boolean $annonceSelectionne
     *
     * @ORM\Column(name="annonceSelectionne", type="boolean", nullable=true)
     */
    private $annonceSelectionne;

    /**
     * @var \DateTime $dateDebut
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime $dateFin
     *
     * @ORM\Column(name="dateFin", type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @var \DateTime $anneAudit
     *
     * @ORM\Column(name="anneAudit", type="date", nullable=true)
     */
    private $anneAudit;

    /**
     * @var string $audit
     *
     * @ORM\Column(name="audit", type="text", nullable=true)
     * @Assert\Choice(callback = "getAuditType")
     */
    private $audit;

    /**
     * @ORM\OneToMany(targetEntity="ClientContact", mappedBy="etude")
     */
    private $clientContacts;

    /**
     * @ORM\OneToMany(targetEntity="Candidature", mappedBy="etude")
     */
    private $candidatures;

    /**
     * @ORM\OneToMany(targetEntity="Ap", mappedBy="etude")
     */
    private $aps;
    
    /**
     * @ORM\OneToMany(targetEntity="Phase", mappedBy="etude")
     */
    private $phases;

    /**
     * @ORM\OneToMany(targetEntity="Cc", mappedBy="etude")
     */
    private $ccs;

    /**
     * @ORM\OneToMany(targetEntity="Mission", mappedBy="etude")
     */
    private $missions;

    /**
     * @ORM\OneToMany(targetEntity="Suivi", mappedBy="etude")
     */
    private $suivis;

    /**
     * @ORM\OneToMany(targetEntity="Pvi", mappedBy="etude")
     */
    private $pvis;

    /**
     * @ORM\OneToMany(targetEntity="Facture", mappedBy="etude")
     */
    private $factures;

    /**
     * @ORM\OneToMany(targetEntity="Av", mappedBy="etude")
     */
    private $avs;

    /**
     * @ORM\OneToMany(targetEntity="AvMission", mappedBy="etude")
     */
    private $avMissions;

    /**
     * @ORM\OneToMany(targetEntity="Pvr", mappedBy="etude")
     */
    private $pvrs;

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
      * @ORM\prePersist
      */
     public function prePersist()
     {
         $this->dateCreation = new \DateTime();
     }

    /**
     * Set suiveur
     *
     * @param mgate\PersonneBundle\Entity\User $suiveur
     * @return Etude
     */
    public function setSuiveur(mgate\PersonneBundle\Entity\User $suiveur)
    {
        $this->suiveur = $suiveur;
    
        return $this;
    }

    /**
     * Get suiveur
     *
     * @return mgate\PersonneBundle\Entity\User 
     */
    public function getSuiveur()
    {
        return $this->suiveur;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Etude
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    
        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set mandat
     *
     * @param integer $mandat
     * @return Etude
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
     * Set num
     *
     * @param integer $num
     * @return Etude
     */
    public function setNum($num)
    {
        $this->num = $num;
    
        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set dossierCree
     *
     * @param boolean $dossierCree
     * @return Etude
     */
    public function setDossierCree($dossierCree)
    {
        $this->dossierCree = $dossierCree;
    
        return $this;
    }

    /**
     * Get dossierCree
     *
     * @return boolean 
     */
    public function getDossierCree()
    {
        return $this->dossierCree;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Etude
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
     * Set competences
     *
     * @param string $competences
     * @return Etude
     */
    public function setCompetences($competences)
    {
        $this->competences = $competences;
    
        return $this;
    }

    /**
     * Get competences
     *
     * @return string 
     */
    public function getCompetences()
    {
        return $this->competences;
    }

    /**
     * Set deonto
     *
     * @param boolean $deonto
     * @return Etude
     */
    public function setDeonto($deonto)
    {
        $this->deonto = $deonto;
    
        return $this;
    }

    /**
     * Get deonto
     *
     * @return boolean 
     */
    public function getDeonto()
    {
        return $this->deonto;
    }

    /**
     * Set mailEntretienEnvoye
     *
     * @param boolean $mailEntretienEnvoye
     * @return Etude
     */
    public function setMailEntretienEnvoye($mailEntretienEnvoye)
    {
        $this->mailEntretienEnvoye = $mailEntretienEnvoye;
    
        return $this;
    }

    /**
     * Get mailEntretienEnvoye
     *
     * @return boolean 
     */
    public function getMailEntretienEnvoye()
    {
        return $this->mailEntretienEnvoye;
    }

    /**
     * Set annonceSelectionne
     *
     * @param boolean $annonceSelectionne
     * @return Etude
     */
    public function setAnnonceSelectionne($annonceSelectionne)
    {
        $this->annonceSelectionne = $annonceSelectionne;
    
        return $this;
    }

    /**
     * Get annonceSelectionne
     *
     * @return boolean 
     */
    public function getAnnonceSelectionne()
    {
        return $this->annonceSelectionne;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Etude
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
     * @return Etude
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
     * Set anneAudit
     *
     * @param \DateTime $anneAudit
     * @return Etude
     */
    public function setAnneAudit($anneAudit)
    {
        $this->anneAudit = $anneAudit;
    
        return $this;
    }

    /**
     * Get anneAudit
     *
     * @return \DateTime 
     */
    public function getAnneAudit()
    {
        return $this->anneAudit;
    }

    /**
     * Set audit
     *
     * @param string $audit
     * @return Etude
     */
    public function setAudit($audit)
    {
        $this->audit = $audit;
    
        return $this;
    }

    /**
     * Get audit
     *
     * @return string 
     */
    public function getAudit()
    {
        return $this->audit;
    }

    /**
     * Set clientContacts
     *
     * @param string $clientContacts
     * @return Etude
     */
    public function setClientContacts($clientContacts)
    {
        $this->clientContacts = $clientContacts;
    
        return $this;
    }

    /**
     * Get clientContacts
     *
     * @return string 
     */
    public function getClientContacts()
    {
        return $this->clientContacts;
    }

    /**
     * Set candidatures
     *
     * @param string $candidatures
     * @return Etude
     */
    public function setCandidatures($candidatures)
    {
        $this->candidatures = $candidatures;
    
        return $this;
    }

    /**
     * Get candidatures
     *
     * @return string 
     */
    public function getCandidatures()
    {
        return $this->candidatures;
    }

    /**
     * Set aps
     *
     * @param string $aps
     * @return Etude
     */
    public function setAps($aps)
    {
        $this->aps = $aps;
    
        return $this;
    }

    /**
     * Get aps
     *
     * @return string 
     */
    public function getAps()
    {
        return $this->aps;
    }

    /**
     * Set ccs
     *
     * @param string $ccs
     * @return Etude
     */
    public function setCcs($ccs)
    {
        $this->ccs = $ccs;
    
        return $this;
    }

    /**
     * Get ccs
     *
     * @return string 
     */
    public function getCcs()
    {
        return $this->ccs;
    }

    /**
     * Set missions
     *
     * @param string $missions
     * @return Etude
     */
    public function setMissions($missions)
    {
        $this->missions = $missions;
    
        return $this;
    }

    /**
     * Get missions
     *
     * @return string 
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * Set suivis
     *
     * @param string $suivis
     * @return Etude
     */
    public function setSuivis($suivis)
    {
        $this->suivis = $suivis;
    
        return $this;
    }

    /**
     * Get suivis
     *
     * @return string 
     */
    public function getSuivis()
    {
        return $this->suivis;
    }

    /**
     * Set pvis
     *
     * @param string $pvis
     * @return Etude
     */
    public function setPvis($pvis)
    {
        $this->pvis = $pvis;
    
        return $this;
    }

    /**
     * Get pvis
     *
     * @return string 
     */
    public function getPvis()
    {
        return $this->pvis;
    }

    /**
     * Set fis
     *
     * @param string $fis
     * @return Etude
     */
    public function setFis($fis)
    {
        $this->fis = $fis;
    
        return $this;
    }

    /**
     * Get fis
     *
     * @return string 
     */
    public function getFis()
    {
        return $this->fis;
    }

    /**
     * Set avs
     *
     * @param string $avs
     * @return Etude
     */
    public function setAvs($avs)
    {
        $this->avs = $avs;
    
        return $this;
    }

    /**
     * Get avs
     *
     * @return string 
     */
    public function getAvs()
    {
        return $this->avs;
    }

    /**
     * Set avMissions
     *
     * @param string $avMissions
     * @return Etude
     */
    public function setAvMissions($avMissions)
    {
        $this->avMissions = $avMissions;
    
        return $this;
    }

    /**
     * Get avMissions
     *
     * @return string 
     */
    public function getAvMissions()
    {
        return $this->avMissions;
    }

    /**
     * Set pvrs
     *
     * @param string $pvrs
     * @return Etude
     */
    public function setPvrs($pvrs)
    {
        $this->pvrs = $pvrs;
    
        return $this;
    }

    /**
     * Get pvrs
     *
     * @return string 
     */
    public function getPvrs()
    {
        return $this->pvrs;
    }

    /**
     * Set fss
     *
     * @param string $fss
     * @return Etude
     */
    public function setFss($fss)
    {
        $this->fss = $fss;
    
        return $this;
    }

    /**
     * Get fss
     *
     * @return string 
     */
    public function getFss()
    {
        return $this->fss;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clientContacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->candidatures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ccs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->missions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->suivis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pvis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avMissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pvrs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fss = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add clientContacts
     *
     * @param mgate\SuiviBundle\Entity\ClientContact $clientContacts
     * @return Etude
     */
    public function addClientContact(\mgate\SuiviBundle\Entity\ClientContact $clientContacts)
    {
        $this->clientContacts[] = $clientContacts;
    
        return $this;
    }

    /**
     * Remove clientContacts
     *
     * @param mgate\SuiviBundle\Entity\ClientContact $clientContacts
     */
    public function removeClientContact(\mgate\SuiviBundle\Entity\ClientContact $clientContacts)
    {
        $this->clientContacts->removeElement($clientContacts);
    }

    /**
     * Add candidatures
     *
     * @param mgate\SuiviBundle\Entity\Candidature $candidatures
     * @return Etude
     */
    public function addCandidature(\mgate\SuiviBundle\Entity\Candidature $candidatures)
    {
        $this->candidatures[] = $candidatures;
    
        return $this;
    }

    /**
     * Remove candidatures
     *
     * @param mgate\SuiviBundle\Entity\Candidature $candidatures
     */
    public function removeCandidature(\mgate\SuiviBundle\Entity\Candidature $candidatures)
    {
        $this->candidatures->removeElement($candidatures);
    }

    /**
     * Add aps
     *
     * @param mgate\SuiviBundle\Entity\Ap $aps
     * @return Etude
     */
    public function addAp(\mgate\SuiviBundle\Entity\Ap $aps)
    {
        $this->aps[] = $aps;
    
        return $this;
    }

    /**
     * Remove aps
     *
     * @param mgate\SuiviBundle\Entity\Ap $aps
     */
    public function removeAp(\mgate\SuiviBundle\Entity\Ap $aps)
    {
        $this->aps->removeElement($aps);
    }

    /**
     * Add ccs
     *
     * @param mgate\SuiviBundle\Entity\Cc $ccs
     * @return Etude
     */
    public function addCc(\mgate\SuiviBundle\Entity\Cc $ccs)
    {
        $this->ccs[] = $ccs;
    
        return $this;
    }

    /**
     * Remove ccs
     *
     * @param mgate\SuiviBundle\Entity\Cc $ccs
     */
    public function removeCc(\mgate\SuiviBundle\Entity\Cc $ccs)
    {
        $this->ccs->removeElement($ccs);
    }

    /**
     * Add missions
     *
     * @param mgate\SuiviBundle\Entity\Mission $missions
     * @return Etude
     */
    public function addMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions[] = $missions;
    
        return $this;
    }

    /**
     * Remove missions
     *
     * @param mgate\SuiviBundle\Entity\Mission $missions
     */
    public function removeMission(\mgate\SuiviBundle\Entity\Mission $missions)
    {
        $this->missions->removeElement($missions);
    }

    /**
     * Add suivis
     *
     * @param mgate\SuiviBundle\Entity\Suivi $suivis
     * @return Etude
     */
    public function addSuivi(\mgate\SuiviBundle\Entity\Suivi $suivis)
    {
        $this->suivis[] = $suivis;
    
        return $this;
    }

    /**
     * Remove suivis
     *
     * @param mgate\SuiviBundle\Entity\Suivi $suivis
     */
    public function removeSuivi(\mgate\SuiviBundle\Entity\Suivi $suivis)
    {
        $this->suivis->removeElement($suivis);
    }

    /**
     * Add pvis
     *
     * @param mgate\SuiviBundle\Entity\Pvi $pvis
     * @return Etude
     */
    public function addPvi(\mgate\SuiviBundle\Entity\Pvi $pvis)
    {
        $this->pvis[] = $pvis;
    
        return $this;
    }

    /**
     * Remove pvis
     *
     * @param mgate\SuiviBundle\Entity\Pvi $pvis
     */
    public function removePvi(\mgate\SuiviBundle\Entity\Pvi $pvis)
    {
        $this->pvis->removeElement($pvis);
    }

    /**
     * Add fis
     *
     * @param mgate\SuiviBundle\Entity\Fi $fis
     * @return Etude
     */
    public function addFi(\mgate\SuiviBundle\Entity\Fi $fis)
    {
        $this->fis[] = $fis;
    
        return $this;
    }

    /**
     * Remove fis
     *
     * @param mgate\SuiviBundle\Entity\Fi $fis
     */
    public function removeFi(\mgate\SuiviBundle\Entity\Fi $fis)
    {
        $this->fis->removeElement($fis);
    }

    /**
     * Add avs
     *
     * @param mgate\SuiviBundle\Entity\Av $avs
     * @return Etude
     */
    public function addAv(\mgate\SuiviBundle\Entity\Av $avs)
    {
        $this->avs[] = $avs;
    
        return $this;
    }

    /**
     * Remove avs
     *
     * @param mgate\SuiviBundle\Entity\Av $avs
     */
    public function removeAv(\mgate\SuiviBundle\Entity\Av $avs)
    {
        $this->avs->removeElement($avs);
    }

    /**
     * Add avMissions
     *
     * @param mgate\SuiviBundle\Entity\AvMission $avMissions
     * @return Etude
     */
    public function addAvMission(\mgate\SuiviBundle\Entity\AvMission $avMissions)
    {
        $this->avMissions[] = $avMissions;
    
        return $this;
    }

    /**
     * Remove avMissions
     *
     * @param mgate\SuiviBundle\Entity\AvMission $avMissions
     */
    public function removeAvMission(\mgate\SuiviBundle\Entity\AvMission $avMissions)
    {
        $this->avMissions->removeElement($avMissions);
    }

    /**
     * Add pvrs
     *
     * @param mgate\SuiviBundle\Entity\Pvr $pvrs
     * @return Etude
     */
    public function addPvr(\mgate\SuiviBundle\Entity\Pvr $pvrs)
    {
        $this->pvrs[] = $pvrs;
    
        return $this;
    }

    /**
     * Remove pvrs
     *
     * @param mgate\SuiviBundle\Entity\Pvr $pvrs
     */
    public function removePvr(\mgate\SuiviBundle\Entity\Pvr $pvrs)
    {
        $this->pvrs->removeElement($pvrs);
    }

    /**
     * Add fss
     *
     * @param mgate\SuiviBundle\Entity\Fs $fss
     * @return Etude
     */
    public function addFs(\mgate\SuiviBundle\Entity\Fs $fss)
    {
        $this->fss[] = $fss;
    
        return $this;
    }

    /**
     * Remove fss
     *
     * @param mgate\SuiviBundle\Entity\Fs $fss
     */
    public function removeFs(\mgate\SuiviBundle\Entity\Fs $fss)
    {
        $this->fss->removeElement($fss);
    }

    /**
     * Set prospect
     *
     * @param mgate\PersonneBundle\Entity\Prospect $prospect
     * @return Etude
     */
    public function setProspect(\mgate\PersonneBundle\Entity\Prospect $prospect)
    {
        $this->prospect = $prospect;
    
        return $this;
    }

    /**
     * Get prospect
     *
     * @return mgate\PersonneBundle\Entity\Prospect 
     */
    public function getProspect()
    {
        return $this->prospect;
    }
    
    public static function getAuditType()
    {
        return array('Déontologique', 'Exhaustive');
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Etude
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Add phases
     *
     * @param \mgate\SuiviBundle\Entity\Phase $phases
     * @return Etude
     */
    public function addPhase(\mgate\SuiviBundle\Entity\Phase $phases)
    {
        $this->phases[] = $phases;
    
        return $this;
    }

    /**
     * Remove phases
     *
     * @param \mgate\SuiviBundle\Entity\Phase $phases
     */
    public function removePhase(\mgate\SuiviBundle\Entity\Phase $phases)
    {
        $this->phases->removeElement($phases);
    }

    /**
     * Get phases
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * Add factures
     *
     * @param \mgate\SuiviBundle\Entity\Facture $factures
     * @return Etude
     */
    public function addFacture(\mgate\SuiviBundle\Entity\Facture $factures)
    {
        $this->factures[] = $factures;
    
        return $this;
    }

    /**
     * Remove factures
     *
     * @param \mgate\SuiviBundle\Entity\Facture $factures
     */
    public function removeFacture(\mgate\SuiviBundle\Entity\Facture $factures)
    {
        $this->factures->removeElement($factures);
    }

    /**
     * Get factures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFactures()
    {
        return $this->factures;
    }
}