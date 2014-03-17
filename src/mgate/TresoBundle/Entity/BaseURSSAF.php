<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseURSSAF
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\TresoBundle\Entity\BaseURSSAFRepository")
 */
class BaseURSSAF
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
     * @var string
     *
     * @ORM\Column(name="baseURSSAF", type="decimal", precision=4, scale=2)
     */
    private $baseURSSAF;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date")
     */
    private $dateFin;


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
     * Set baseURSSAF
     *
     * @param string $baseURSSAF
     * @return BaseURSSAF
     */
    public function setBaseURSSAF($baseURSSAF)
    {
        $this->baseURSSAF = $baseURSSAF;
    
        return $this;
    }

    /**
     * Get baseURSSAF
     *
     * @return string 
     */
    public function getBaseURSSAF()
    {
        return $this->baseURSSAF;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return BaseURSSAF
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
     * @return BaseURSSAF
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
}
