<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepartitionJEH
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\RepartitionJEHRepository")
 */
class RepartitionJEH
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Mission", inversedBy="RepartitionJEH") 
     */
    private $mission;


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
     * Set mission
     *
     * @param \mgate\SuiviBundle\Entity\Mission $mission
     * @return RepartitionJEH
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
}