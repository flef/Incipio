<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BV
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"mandat", "numero"})})
 * @ORM\Entity(repositoryClass="mgate\TresoBundle\Entity\BVRepository")
 */
class BV
{
    // TODO Liée au répartition JEH laisser le choix d'ajouter des existantes (une fois que les avenants seront OK)
    // si plusieur repartition, moyenner
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="mandat", type="smallint")
     */
    private $mandat;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="smallint")
     */
    private $numero;
        
    /**
     * @var integer
     *
     * @ORM\Column(name="nombreJEH", type="smallint")
     */
    private $nombreJEH;

    /**
     * @var float
     *
     * @ORM\Column(name="remunerationBruteParJEH", type="float")
     */
    private $remunerationBruteParJEH;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeVersement", type="date")
     */
    private $dateDeVersement;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDemission", type="date")
     */
    private $dateDemission;

    /**
     * @var string
     *
     * @ORM\Column(name="typeDeTravail", type="string", length=255)
     */
    private $typeDeTravail;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission")
     */
    private $mission;

    /**
     * @var float
     *
     * @ORM\Column(name="baseURSSAF", type="float")
     */
    private $baseURSSAF;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxJuniorAssietteDeCotisation", type="float")
     */
    private $tauxJuniorAssietteDeCotisation;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxJuniorRemunerationBrute", type="float")
     */
    private $tauxJuniorRemunerationBrute;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxEtudiantAssietteDeCotisation", type="float")
     */
    private $tauxEtudiantAssietteDeCotisation;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxEtudiantRemunerationBrute", type="float")
     */
    private $tauxEtudiantRemunerationBrute;

    

    /**
     * @var string
     *
     * @ORM\Column(name="numeroVirement", type="string", length=255)
     */
    private $numeroVirement;
    
    //GETTER ADITION
    public function getPartJunior(){
        return round($this->nombreJEH  * ($this->baseURSSAF * $this->tauxJuniorAssietteDeCotisation 
            + $this->tauxJuniorRemunerationBrute * $this->remunerationBruteParJEH),2);
    }
    public function getPartEtudiant(){
        return round($this->nombreJEH  * ($this->baseURSSAF * $this->tauxEtudiantAssietteDeCotisation
            + $this->tauxEtudiantRemunerationBrute * $this->remunerationBruteParJEH),2);
    }
    public function getReference(){
        return $this->mandat.'-BV-'.sprintf('%1$02d',$this->numero);
    }
    public function getRemunerationBrute(){
        return $this->getRemunerationBruteParJEH() * $this->nombreJEH;
    }
    
    ///////

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
     * Set dateDeVersement
     *
     * @param \DateTime $dateDeVersement
     * @return BV
     */
    public function setDateDeVersement($dateDeVersement)
    {
        $this->dateDeVersement = $dateDeVersement;
    
        return $this;
    }

    /**
     * Get dateDeVersement
     *
     * @return \DateTime 
     */
    public function getDateDeVersement()
    {
        return $this->dateDeVersement;
    }

    /**
     * Set typeDeTravail
     *
     * @param string $typeDeTravail
     * @return BV
     */
    public function setTypeDeTravail($typeDeTravail)
    {
        $this->typeDeTravail = $typeDeTravail;
    
        return $this;
    }

    /**
     * Get typeDeTravail
     *
     * @return string 
     */
    public function getTypeDeTravail()
    {
        return $this->typeDeTravail;
    }

    /**
     * Set mandat
     *
     * @param integer $mandat
     * @return BV
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
     * @return BV
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
     * Set baseURSSAF
     *
     * @param float $baseURSSAF
     * @return BV
     */
    public function setBaseURSSAF($baseURSSAF)
    {
        $this->baseURSSAF = $baseURSSAF;
    
        return $this;
    }

    /**
     * Get baseURSSAF
     *
     * @return float 
     */
    public function getBaseURSSAF()
    {
        return $this->baseURSSAF;
    }

    /**
     * Set tauxJuniorAssietteDeCotisation
     *
     * @param float $tauxJuniorAssietteDeCotisation
     * @return BV
     */
    public function setTauxJuniorAssietteDeCotisation($tauxJuniorAssietteDeCotisation)
    {
        $this->tauxJuniorAssietteDeCotisation = $tauxJuniorAssietteDeCotisation;
    
        return $this;
    }

    /**
     * Get tauxJuniorAssietteDeCotisation
     *
     * @return float 
     */
    public function getTauxJuniorAssietteDeCotisation()
    {
        return $this->tauxJuniorAssietteDeCotisation;
    }

    /**
     * Set tauxJuniorRemunerationBrute
     *
     * @param float $tauxJuniorRemunerationBrute
     * @return BV
     */
    public function setTauxJuniorRemunerationBrute($tauxJuniorRemunerationBrute)
    {
        $this->tauxJuniorRemunerationBrute = $tauxJuniorRemunerationBrute;
    
        return $this;
    }

    /**
     * Get tauxJuniorRemunerationBrute
     *
     * @return float 
     */
    public function getTauxJuniorRemunerationBrute()
    {
        return $this->tauxJuniorRemunerationBrute;
    }

    /**
     * Set tauxEtudiantAssietteDeCotisation
     *
     * @param float $tauxEtudiantAssietteDeCotisation
     * @return BV
     */
    public function setTauxEtudiantAssietteDeCotisation($tauxEtudiantAssietteDeCotisation)
    {
        $this->tauxEtudiantAssietteDeCotisation = $tauxEtudiantAssietteDeCotisation;
    
        return $this;
    }

    /**
     * Get tauxEtudiantAssietteDeCotisation
     *
     * @return float 
     */
    public function getTauxEtudiantAssietteDeCotisation()
    {
        return $this->tauxEtudiantAssietteDeCotisation;
    }

    /**
     * Set tauxEtudiantRemunerationBrute
     *
     * @param float $tauxEtudiantRemunerationBrute
     * @return BV
     */
    public function setTauxEtudiantRemunerationBrute($tauxEtudiantRemunerationBrute)
    {
        $this->tauxEtudiantRemunerationBrute = $tauxEtudiantRemunerationBrute;
    
        return $this;
    }

    /**
     * Get tauxEtudiantRemunerationBrute
     *
     * @return float 
     */
    public function getTauxEtudiantRemunerationBrute()
    {
        return $this->tauxEtudiantRemunerationBrute;
    }

    /**
     * Set nombreJEH
     *
     * @param integer $nombreJEH
     * @return BV
     */
    public function setNombreJEH($nombreJEH)
    {
        $this->nombreJEH = $nombreJEH;
    
        return $this;
    }

    /**
     * Get nombreJEH
     *
     * @return integer 
     */
    public function getNombreJEH()
    {
        return $this->nombreJEH;
    }

    /**
     * Set numeroVirement
     *
     * @param string $numeroVirement
     * @return BV
     */
    public function setNumeroVirement($numeroVirement)
    {
        $this->numeroVirement = $numeroVirement;
    
        return $this;
    }

    /**
     * Get numeroVirement
     *
     * @return string 
     */
    public function getNumeroVirement()
    {
        return $this->numeroVirement;
    }

    /**
     * Set remunerationBruteParJEH
     *
     * @param float $remunerationBruteParJEH
     * @return BV
     */
    public function setRemunerationBruteParJEH($remunerationBruteParJEH)
    {
        $this->remunerationBruteParJEH = $remunerationBruteParJEH;
    
        return $this;
    }

    /**
     * Get remunerationBruteParJEH
     *
     * @return float 
     */
    public function getRemunerationBruteParJEH()
    {
        return $this->remunerationBruteParJEH;
    }

    /**
     * Set mission
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     * @return BV
     */
    public function setMission(\mgate\SuiviBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;
    
        return $this;
    }

    /**
     * Get mission
     *
     * @return \mgate\SuiviBundle\Entity\Mission 
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set dateDemission
     *
     * @param \DateTime $dateDemission
     * @return BV
     */
    public function setDateDemission($dateDemission)
    {
        $this->dateDemission = $dateDemission;
    
        return $this;
    }

    /**
     * Get dateDemission
     *
     * @return \DateTime 
     */
    public function getDateDemission()
    {
        return $this->dateDemission;
    }
}