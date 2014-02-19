<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\FactureVente
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FactureVente extends DocType
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
     * @var integer $num
     *
     * @ORM\Column(name="num", type="integer", nullable=true)
     */
    private $num;
    
    /**
     * @var integer $exercice
     *
     * @ORM\Column(name="exercice", type="integer", nullable=true)
     */
    private $exercice;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="FactureVentes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;
    
    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="text", nullable=false)
     */
    private $type;

    /**
     * @var float $montantHT
     *
     * @ORM\Column(name="montantHT", type="decimal", scale=2, nullable=true)
     */
    private $montantHT;
    
    /**
     * @var float $tauxTVA
     *
     * @ORM\Column(name="tauxTVA", type="decimal", scale=3, nullable=true)
     */
    private $tauxTVA;

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
     * @return FactureVente
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
     * @return FactureVente
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
     * @return double
     */
    public function getMontantHT()
    {
        return $this->montantHT;
    }
    
    /**
     * Get montantTTC
     *
     * @return double
     */
    public function getMontantTTC()
    {
        return $this->montantHT * (1 + $this->tauxTVA);
    }

    /**
     * Set montantHT
     *
     * @param double $montantHT
     * @return FactureVente
     */
    public function setMontantHT($montantHT)
    {
        $this->montantHT = $montantHT;
    
        return $this;
    }
    
    /**
     * Get tauxTVA
     * @example / 0.196
     * @return double
     */
    public function getTauxTVA()
    {
        return $this->montantHT;
    }

    /**
     * Set tauxTVA
     *
     * @param taux $tauxTVA
     * @return FactureVente
     */
    public function setTauxTVA($tauxTVA)
    {
        $this->montantHT = $tauxTVA;
    
        return $this;
    }

    /**
     * Set num
     *
     * @param integer $num
     * @return FactureVente
     */
    public function setNum($num)
    {
        $this->num = $num;
    
        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set exercice
     *
     * @param integer $exercice
     * @return FactureVente
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;
    
        return $this;
    }

    /**
     * Get exercice
     *
     * @return integer 
     */
    public function getExercice()
    {
        return $this->exercice;
    }
}