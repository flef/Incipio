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
}