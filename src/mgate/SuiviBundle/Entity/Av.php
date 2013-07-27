<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\Av
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\AvRepository")
 */
class Av extends DocType
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="avs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;
    
    /**
     * @ORM\Column(name="differentielDelai", type="integer", nullable=false,  options={"default"=0})
     * @var date
     */
    private $differentielDelai;
    
    /**
     * @ORM\Column(name="objet", type="text", nullable=false)
     * @var string
     */
    private $objet;
    
    /**
     * @var AvMission $avenantsMissions
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\AvMission", mappedBy="Av", cascade={"persist","remove"})
     */
    private $avenantsMissions;
    
    /**
     * @var array $clauses
     * @ORM\Column(name="clauses", type="array")
     */
    private $clauses;
    
    /**
     * @var Collection phase differentiel
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\Phase", mappedBy="avenant", cascade={"persist", "remove"})
     */
    private $phases;
    
    public static function getClausesChoices(){
        return array(1 => 'Avenant de Délai',
            2 => 'Avenant de Méthodologie',
            3 => 'Avenant de Montant',
            4 => 'Avenant de Mission',
            5 => 'Avenant de Rupture');
    }
    
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
     * @return Av
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
        $this->avenantsMissions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set differentielDelai
     *
     * @param integer $differentielDelai
     * @return Av
     */
    public function setDifferentielDelai($differentielDelai)
    {
        $this->differentielDelai = $differentielDelai;
    
        return $this;
    }

    /**
     * Get differentielDelai
     *
     * @return integer 
     */
    public function getDifferentielDelai()
    {
        return $this->differentielDelai;
    }

    /**
     * Set objet
     *
     * @param string $objet
     * @return Av
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
     * Add avenantsMissions
     *
     * @param \mgate\SuiviBundle\Entity\AvMission $avenantsMissions
     * @return Av
     */
    public function addAvenantsMission(\mgate\SuiviBundle\Entity\AvMission $avenantsMissions)
    {
        $this->avenantsMissions[] = $avenantsMissions;
    
        return $this;
    }

    /**
     * Remove avenantsMissions
     *
     * @param \mgate\SuiviBundle\Entity\AvMission $avenantsMissions
     */
    public function removeAvenantsMission(\mgate\SuiviBundle\Entity\AvMission $avenantsMissions)
    {
        $this->avenantsMissions->removeElement($avenantsMissions);
    }

    /**
     * Get avenantsMissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvenantsMissions()
    {
        return $this->avenantsMissions;
    }

    /**
     * Set clauses
     *
     * @param array $clauses
     * @return Av
     */
    public function setClauses($clauses)
    {
        $this->clauses = $clauses;
    
        return $this;
    }

    /**
     * Get clauses
     *
     * @return array 
     */
    public function getClauses()
    {
        return $this->clauses;
    }

    /**
     * Add phases
     *
     * @param \mgate\SuiviBundle\Entity\Phase $phases
     * @return Av
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
}