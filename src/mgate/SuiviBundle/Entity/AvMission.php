<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\AvMission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\AvMissionRepository")
 */
class AvMission extends DocType
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
     * @var Mission
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission")
     */
    private $mission;
    
    /**
     * @var integer $nouvelleRepartition
     * @ORM\OneToMany(targetEntity="mgate\SuiviBundle\Entity\RepartitionJEH", mappedBy="AvMission", cascade={"persist","remove"})
     */
    private $nouvelleRepartition;
    
    /**
     * @var interger $nouveauPourcentage
     * @ORM\Column(name="nouveauPourcentage", type="integer")
     */
    private $nouveauPourcentage;

    /**
     * @var integer $differentielDelai
     * @ORM\Column(name="differentielDelai", type="integer")
     */
    private $differentielDelai;

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
        $this->nouvelleRepartition = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set differentielDelai
     *
     * @param integer $differentielDelai
     * @return AvMission
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
     * Add nouvelleRepartition
     *
     * @param \mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition
     * @return AvMission
     */
    public function addNouvelleRepartition(\mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition)
    {
        $this->nouvelleRepartition[] = $nouvelleRepartition;
    
        return $this;
    }

    /**
     * Remove nouvelleRepartition
     *
     * @param \mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition
     */
    public function removeNouvelleRepartition(\mgate\SuiviBundle\Entity\RepartitionJEH $nouvelleRepartition)
    {
        $this->nouvelleRepartition->removeElement($nouvelleRepartition);
    }

    /**
     * Get nouvelleRepartition
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNouvelleRepartition()
    {
        return $this->nouvelleRepartition;
    }

    /**
     * Set mission
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     * @return AvMission
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
     * Set nouveauPourcentage
     *
     * @param integer $nouveauPourcentage
     * @return AvMission
     */
    public function setNouveauPourcentage($nouveauPourcentage)
    {
        $this->nouveauPourcentage = $nouveauPourcentage;
    
        return $this;
    }

    /**
     * Get nouveauPourcentage
     *
     * @return integer 
     */
    public function getNouveauPourcentage()
    {
        return $this->nouveauPourcentage;
    }
}