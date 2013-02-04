<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\Facture
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\FactureRepository")
 */
class Facture extends DocType
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
     * @ORM\ManyToOne(targetEntity="Etude", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;
    
    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="text", nullable=true)
     */
    private $type;

    /**
     * @var integer $montantHT
     *
     * @ORM\Column(name="montantHT", type="integer", nullable=true)
     */
    private $montantHT;

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
     * @return Fi
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
     * Set type
     *
     * @param string $type
     * @return Etude
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Get montantHT
     *
     * @return integer 
     */
    public function getMontantHT()
    {
        return $this->montantHT;
    }

    /**
     * Set montantHT
     *
     * @param string $montantHT
     * @return Facture
     */
    public function setMontantHT($montantHT)
    {
        $this->montantHT = $montantHT;
    
        return $this;
    }
}