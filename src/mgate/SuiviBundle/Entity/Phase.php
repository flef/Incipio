<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\SuiviBundle\Entity\Phase
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\PhaseRepository")
 */
class Phase
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="phases", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /**
     * @var integer $nbrJEH
     *
     * @ORM\Column(name="nbrJEH", type="integer")
     */
    private $nbrJEH;
    
    /**
     * @var integer $prixJEH
     *
     * @ORM\Column(name="prixJEH", type="integer")
     * @Assert\Min(80)
     * @Assert\Max(300)
     */
    private $prixJEH;
    
   /**
     * @var string $titre
     *
     * @ORM\Column(name="titre", type="text", nullable=false)
     */
    private $titre;

    /**
     * @var string $objectif
     *
     * @ORM\Column(name="objectif", type="text", nullable=true)
     */
    private $objectif;
    
    /**
     * @var string $methodo
     *
     * @ORM\Column(name="methodo", type="text", nullable=true)
     */
    private $methodo;
    
    /**
     * @var \DateTime $dateDebut
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;
    
    /**
     * @var string $delai
     *
     * @ORM\Column(name="delai", type="text", nullable=true)
     */
    private $delai;
    
    /**
     * @var integer $validation
     *
     * @ORM\Column(name="validation", type="integer", nullable=true)
     * @Assert\Choice(callback = "getValidationChoiceAssert")
     */
    private $validation;

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
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return Ap
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
     * Set nbrJEH
     *
     * @param integer $nbrJEH
     * @return Phase
     */
    public function setNbrJEH($nbrJEH)
    {
        $this->nbrJEH = $nbrJEH;
    
        return $this;
    }

    /**
     * Get nbrJEH
     *
     * @return integer 
     */
    public function getNbrJEH()
    {
        return $this->nbrJEH;
    }

    /**
     * Set prixJEH
     *
     * @param integer $prixJEH
     * @return Phase
     */
    public function setPrixJEH($prixJEH)
    {
        $this->prixJEH = $prixJEH;
    
        return $this;
    }

    /**
     * Get prixJEH
     *
     * @return integer 
     */
    public function getPrixJEH()
    {
        return $this->prixJEH;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Phase
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set objectif
     *
     * @param string $objectif
     * @return Phase
     */
    public function setObjectif($objectif)
    {
        $this->objectif = $objectif;
    
        return $this;
    }

    /**
     * Get objectif
     *
     * @return string 
     */
    public function getObjectif()
    {
        return $this->objectif;
    }

    /**
     * Set methodo
     *
     * @param string $methodo
     * @return Phase
     */
    public function setMethodo($methodo)
    {
        $this->methodo = $methodo;
    
        return $this;
    }

    /**
     * Get methodo
     *
     * @return string 
     */
    public function getMethodo()
    {
        return $this->methodo;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Phase
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
     * Set delai
     *
     * @param string $delai
     * @return Phase
     */
    public function setDelai($delai)
    {
        $this->delai = $delai;
    
        return $this;
    }

    /**
     * Get delai
     *
     * @return string 
     */
    public function getDelai()
    {
        return $this->delai;
    }

    /**
     * Set validation
     *
     * @param integer $validation
     * @return Phase
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
    
        return $this;
    }

    /**
     * Get validation
     *
     * @return integer 
     */
    public function getValidation()
    {
        return $this->validation;
    }
    
    public static function getValidationChoice()
    {
        return array( 0 => "Aucune", 1 => "Cette phase sera soumise à une validation orale lors d’un entretien avec le client.", 2 => "Cette phase sera soumise à une validation écrite qui prend la forme d’un Procès-Verbal Intermédiaire signé par le client.");
    }
    public static function getValidationChoiceAssert()
    {
        return array_keys(Phase::getValidationChoice());
    }
}