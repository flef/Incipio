<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\SuiviBundle\Entity\PhaseMission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\PhaseMissionRepository")
 */
class PhaseMission
{
    ///TODO verification que $phase et $mission sont de la meme etude
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Phase", inversedBy="PhaseMission")
     */
    private $phase;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission", inversedBy="PhaseMission")
     */
    private $mission;
    
    /**
     * @ORM\Column(name="nbrJEH", type="integer", nullable=true)
     */
    private $nbrJEH;


    /**
     * Set nbrJEH
     *
     * @param string $nbrJEH
     * @return PhaseMission
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
     * Set phase
     *
     * @param \mgate\SuiviBundle\Entity\Phase $phase
     * @return PhaseMission
     */
    public function setPhase(\mgate\SuiviBundle\Entity\Phase $phase)
    {
        $this->phase = $phase;
    
        return $this;
    }

    /**
     * Get phase
     *
     * @return \mgate\SuiviBundle\Entity\Phase 
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * Set mission
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     * @return PhaseMission
     */
    public function setMission(\mgate\SuiviBundle\Entity\Mission $mission)
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
}