<?php

namespace mgate\SuiviBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\Ap
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\ApRepository")
 */
class Ap extends DocType
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
     * @ORM\OneToOne(targetEntity="Etude", inversedBy="ap", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;
    
    /** nombre de developpeur estimé
     * @var integer $nbrDev
     *
     * @ORM\Column(name="nbrDev", type="integer", nullable=true)
     */
    private $nbrDev;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $contactMgate;
    
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
     * Set nbrDev
     *
     * @param integer $nbrDev
     * @return Ap
     */
    public function setNbrDev($nbrDev)
    {
        $this->nbrDev = $nbrDev;
    
        return $this;
    }

    /**
     * Get nbrDev
     *
     * @return integer 
     */
    public function getNbrDev()
    {
        return $this->nbrDev;
    }
    
    /**
     * Set contactMgate
     *
     * @param \mgate\PersonneBundle\Entity\Personne $contactMgate
     * @return Ap
     */
    public function setContactMgate(\mgate\PersonneBundle\Entity\Personne $contactMgate = null) {
        $this->contactMgate = $contactMgate;

        return $this;
    }

    /**
     * Get contactMgate
     *
     * @return \mgate\PersonneBundle\Entity\Personne
     */
    public function getContactMgate() {
        return $this->contactMgate;
    }
}