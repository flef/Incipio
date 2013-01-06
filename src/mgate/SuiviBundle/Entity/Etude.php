<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;


/**
 * mgate\SuiviBundle\Entity\Etude
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\EtudeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Etude extends \Symfony\Component\DependencyInjection\ContainerAware
{
    /**
     * @var bool
     */
    private $knownProspect = false;
        
    /**
     *
     */
    private $newProspect;   
    
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Prospect", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $prospect;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $suiveur;

    /**
     * @var \DateTime $dateCreation
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;
    
        /**
     * @var \DateTime $dateModification
     *
     * @ORM\Column(name="dateModification", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $dateModification;

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
     * @ORM\OneToOne(targetEntity="Ap", inversedBy="etude", cascade={"persist", "remove"})
     */
    private $ap;
    
    /**
     * @ORM\OneToMany(targetEntity="Phase", mappedBy="etude", cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $phases;

    /**
     * @ORM\OneToOne(targetEntity="Cc", inversedBy="etude", cascade={"persist"})
     */
    private $cc;

    /**
     * @ORM\OneToMany(targetEntity="Mission", mappedBy="etude", cascade={"persist"})
     */
    private $missions;
    
     /**
     * @ORM\OneToOne(targetEntity="FactureAcompte", cascade={"persist"})
     */
    private $factureAcompte;
    
     /**
     * @ORM\OneToOne(targetEntity="FactureSolde", cascade={"persist"})
     */
    private $factureSolde;

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
     * @var boolean $acompte
     *
     * @ORM\Column(name="acompte", type="boolean", nullable=true)
     */
    private $acompte; 
    
    
    /**
     * @var integer $pourcentageAcompte
     *
     * @ORM\Column(name="pourcentageAcompte", type="integer", nullable=true)
     */
    private $pourcentageAcompte;
    
    /**
     * @var integer $fraisDossier
     *
     * @ORM\Column(name="fraisDossier", type="integer", nullable=true)
     */
    private $fraisDossier;
    
    /**
     * @var text $presentationProjet
     *
     * @ORM\Column(name="presentationProjet", type="text", nullable=true)
     */
    private $presentationProjet;
    
    
    /**
     * @var text $descriptionPrestation
     *
     * @ORM\Column(name="descriptionPrestation", type="text", nullable=true)
     */
    private $descriptionPrestation;
    
    /**
     * @var text $typePrestation
     *
     * @ORM\Column(name="prestation", type="text", nullable=true)
     *
     */
    private $typePrestation;

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clientContacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->candidatures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->phases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->missions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->suivis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pvis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->factures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avMissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pvrs = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->fraisDossier=90;
        $this->pourcentageAcompte=0.40;
    }

    
/// rajout à la main
    public function isKnownProspect()
    {
        return $this->knownProspect;
    }
    public function setKnownProspect($boolean)
    {
        $this->knownProspect = $boolean;
    }
    
    public function getNewProspect()
    {
        return $this->newProspect;
    }
    public function setNewProspect($var)
    {
        $this->newProspect = $var;

    }
/// fin rajout
    
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
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return Etude
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;
    
        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime 
     */
    public function getDateModification()
    {
        return $this->dateModification;
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
     * Set acompte
     *
     * @param boolean $acompte
     * @return Etude
     */
    public function setAcompte($acompte)
    {
        $this->acompte = $acompte;
    
        return $this;
    }

    /**
     * Get acompte
     *
     * @return boolean 
     */
    public function getAcompte()
    {
        return $this->acompte;
    }

    /**
     * Set pourcentageAcompte
     *
     * @param integer $pourcentageAcompte
     * @return Etude
     */
    public function setPourcentageAcompte($pourcentageAcompte)
    {
        $this->pourcentageAcompte = $pourcentageAcompte;
    
        return $this;
    }

    /**
     * Get pourcentageAcompte
     *
     * @return integer 
     */
    public function getPourcentageAcompte()
    {
        return $this->pourcentageAcompte;
    }

    /**
     * Set fraisDossier
     *
     * @param integer $fraisDossier
     * @return Etude
     */
    public function setFraisDossier($fraisDossier)
    {
        $this->fraisDossier = $fraisDossier;
    
        return $this;
    }

    /**
     * Get fraisDossier
     *
     * @return integer 
     */
    public function getFraisDossier()
    {
        return $this->fraisDossier;
    }

    /**
     * Set presentationProjet
     *
     * @param string $presentationProjet
     * @return Etude
     */
    public function setPresentationProjet($presentationProjet)
    {
        $this->presentationProjet = $presentationProjet;
    
        return $this;
    }

    /**
     * Get presentationProjet
     *
     * @return string 
     */
    public function getPresentationProjet()
    {
        return $this->presentationProjet;
    }
    
    /**
     * Set descriptionPrestation
     *
     * @param string $descriptionPrestation
     * @return Etude
     */
    public function setDescriptionPrestation($descriptionPrestation)
    {
        $this->descriptionPrestation = $descriptionPrestation;
    
        return $this;
    }

    /**
     * Get descriptionPrestation
     *
     * @return string 
     */
    public function getDescriptionPrestation()
    {
        return $this->descriptionPrestation;
    }

    /**
     * Set typePrestation
     *
     * @param string $typePrestation
     * @return Etude
     */
    public function setTypePrestation($typePrestation)
    {
        $this->typePrestation = $typePrestation;
    
        return $this;
    }

    /**
     * Get typePrestation
     *
     * @return string 
     */
    public function getTypePrestation()
    {
        return $this->typePrestation;
    }

    /**
     * Set prospect
     *
     * @param \mgate\PersonneBundle\Entity\Prospect $prospect
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
     * @return \mgate\PersonneBundle\Entity\Prospect 
     */
    public function getProspect()
    {
        return $this->prospect;
    }

    /**
     * Set suiveur
     *
     * @param \mgate\PersonneBundle\Entity\Personne $suiveur
     * @return Etude
     */
    public function setSuiveur(\mgate\PersonneBundle\Entity\Personne $suiveur = null)
    {
        $this->suiveur = $suiveur;
    
        return $this;
    }

    /**
     * Get suiveur
     *
     * @return \mgate\PersonneBundle\Entity\Personne
     */
    public function getSuiveur()
    {
        return $this->suiveur;
    }

    /**
     * Add clientContacts
     *
     * @param \mgate\SuiviBundle\Entity\ClientContact $clientContacts
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
     * @param \mgate\SuiviBundle\Entity\ClientContact $clientContacts
     */
    public function removeClientContact(\mgate\SuiviBundle\Entity\ClientContact $clientContacts)
    {
        $this->clientContacts->removeElement($clientContacts);
    }

    /**
     * Get clientContacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClientContacts()
    {
        return $this->clientContacts;
    }

    /**
     * Add candidatures
     *
     * @param \mgate\SuiviBundle\Entity\Candidature $candidatures
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
     * @param \mgate\SuiviBundle\Entity\Candidature $candidatures
     */
    public function removeCandidature(\mgate\SuiviBundle\Entity\Candidature $candidatures)
    {
        $this->candidatures->removeElement($candidatures);
    }

    /**
     * Get candidatures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCandidatures()
    {
        return $this->candidatures;
    }

    /**
     * Set ap
     *
     * @param \mgate\SuiviBundle\Entity\Ap $ap
     * @return Etude
     */
    public function setAp(\mgate\SuiviBundle\Entity\Ap $ap = null)
    {
        if($ap!=null)
            $ap->setEtude($this);
        
        $this->ap = $ap;
    
        return $this;
    }

    /**
     * Get ap
     *
     * @return \mgate\SuiviBundle\Entity\Ap 
     */
    public function getAp()
    {
        return $this->ap;
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
     * Set cc
     *
     * @param \mgate\SuiviBundle\Entity\Cc $cc
     * @return Etude
     */
    public function setCc(\mgate\SuiviBundle\Entity\Cc $cc = null)
    {
        if($cc!=null)
            $cc->setEtude($this);
        
        $this->cc = $cc;
    
        return $this;
    }

    /**
     * Get cc
     *
     * @return \mgate\SuiviBundle\Entity\Cc 
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Add missions
     *
     * @param \mgate\SuiviBundle\Entity\Mission $missions
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
     * Add suivis
     *
     * @param \mgate\SuiviBundle\Entity\Suivi $suivis
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
     * @param \mgate\SuiviBundle\Entity\Suivi $suivis
     */
    public function removeSuivi(\mgate\SuiviBundle\Entity\Suivi $suivis)
    {
        $this->suivis->removeElement($suivis);
    }

    /**
     * Get suivis
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSuivis()
    {
        return $this->suivis;
    }

    /**
     * Add pvis
     *
     * @param \mgate\SuiviBundle\Entity\Pvi $pvis
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
     * @param \mgate\SuiviBundle\Entity\Pvi $pvis
     */
    public function removePvi(\mgate\SuiviBundle\Entity\Pvi $pvis)
    {
        $this->pvis->removeElement($pvis);
    }

    /**
     * Get pvis
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPvis()
    {
        return $this->pvis;
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

    /**
     * Add avs
     *
     * @param \mgate\SuiviBundle\Entity\Av $avs
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
     * @param \mgate\SuiviBundle\Entity\Av $avs
     */
    public function removeAv(\mgate\SuiviBundle\Entity\Av $avs)
    {
        $this->avs->removeElement($avs);
    }

    /**
     * Get avs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvs()
    {
        return $this->avs;
    }

    /**
     * Add avMissions
     *
     * @param \mgate\SuiviBundle\Entity\AvMission $avMissions
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
     * @param \mgate\SuiviBundle\Entity\AvMission $avMissions
     */
    public function removeAvMission(\mgate\SuiviBundle\Entity\AvMission $avMissions)
    {
        $this->avMissions->removeElement($avMissions);
    }

    /**
     * Get avMissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvMissions()
    {
        return $this->avMissions;
    }

    /**
     * Add pvrs
     *
     * @param \mgate\SuiviBundle\Entity\Pvr $pvrs
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
     * @param \mgate\SuiviBundle\Entity\Pvr $pvrs
     */
    public function removePvr(\mgate\SuiviBundle\Entity\Pvr $pvrs)
    {
        $this->pvrs->removeElement($pvrs);
    }

    /**
     * Get pvrs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPvrs()
    {
        return $this->pvrs;
    }

    /**
     * Set factureAcompte
     *
     * @param \mgate\SuiviBundle\Entity\FactureAcompte $factureAcompte
     * @return Etude
     */
    public function setFactureAcompte(\mgate\SuiviBundle\Entity\FactureAcompte $factureAcompte = null)
    {
        $this->factureAcompte = $factureAcompte;
    
        return $this;
    }

    /**
     * Get factureAcompte
     *
     * @return \mgate\SuiviBundle\Entity\FactureAcompte 
     */
    public function getFactureAcompte()
    {
        return $this->factureAcompte;
    }

    /**
     * Set factureSolde
     *
     * @param \mgate\SuiviBundle\Entity\FactureSolde $factureSolde
     * @return Etude
     */
    public function setFactureSolde(\mgate\SuiviBundle\Entity\FactureSolde $factureSolde = null)
    {
        $this->factureSolde = $factureSolde;
    
        return $this;
    }

    /**
     * Get factureSolde
     *
     * @return \mgate\SuiviBundle\Entity\FactureSolde 
     */
    public function getFactureSolde()
    {
        return $this->factureSolde;
    }   
}