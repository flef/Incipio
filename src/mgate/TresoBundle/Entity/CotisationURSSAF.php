<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CotisationURSSAF
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\TresoBundle\Entity\CotisationURSSAFRepository")
 */
class CotisationURSSAF
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
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSurBaseURSSAF", type="boolean")
     */
    private $isSurBaseURSSAF;

    /**
     * @var string
     *
     * @ORM\Column(name="tauxPartJE", type="decimal", precision=5, scale=2)
     */
    private $tauxPartJE;

    /**
     * @var string
     *
     * @ORM\Column(name="tauxPartEtu", type="decimal", precision=5, scale=2)
     */
    private $tauxPartEtu;

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
     * Set libelle
     *
     * @param string $libelle
     * @return CotisationURSSAF
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    
        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set isSurBaseURSSAF
     *
     * @param boolean $isSurBaseURSSAF
     * @return CotisationURSSAF
     */
    public function setIsSurBaseURSSAF($isSurBaseURSSAF)
    {
        $this->isSurBaseURSSAF = $isSurBaseURSSAF;
    
        return $this;
    }

    /**
     * Get isSurBaseURSSAF
     *
     * @return boolean 
     */
    public function getIsSurBaseURSSAF()
    {
        return $this->isSurBaseURSSAF;
    }

    /**
     * Set tauxPartJE
     *
     * @param string $tauxPartJE
     * @return CotisationURSSAF
     */
    public function setTauxPartJE($tauxPartJE)
    {
        $this->tauxPartJE = $tauxPartJE;
    
        return $this;
    }

    /**
     * Get tauxPartJE
     *
     * @return string 
     */
    public function getTauxPartJE()
    {
        return $this->tauxPartJE;
    }

    /**
     * Set tauxPartEtu
     *
     * @param string $tauxPartEtu
     * @return CotisationURSSAF
     */
    public function setTauxPartEtu($tauxPartEtu)
    {
        $this->tauxPartEtu = $tauxPartEtu;
    
        return $this;
    }

    /**
     * Get tauxPartEtu
     *
     * @return string 
     */
    public function getTauxPartEtu()
    {
        return $this->tauxPartEtu;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return CotisationURSSAF
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
     * @return CotisationURSSAF
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