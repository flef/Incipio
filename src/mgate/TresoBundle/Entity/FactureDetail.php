<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactureDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FactureDetail
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
     * @ORM\ManyToOne(targetEntity="FactureVente", inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $factureVente;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="montantHT", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $montantHT;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxTVA", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $tauxTVA;

    /**
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumn(nullable=true)
     */
    private $compte;


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
     * Set description
     *
     * @param string $description
     * @return FactureDetail
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set montantHT
     *
     * @param float $montantHT
     * @return FactureDetail
     */
    public function setMontantHT($montantHT)
    {
        $this->montantHT = $montantHT;
    
        return $this;
    }

    /**
     * Get montantHT
     *
     * @return float 
     */
    public function getMontantHT()
    {
        return $this->montantHT;
    }

    /**
     * Set tauxTVA
     *
     * @param float $tauxTVA
     * @return FactureDetail
     */
    public function setTauxTVA($tauxTVA)
    {
        $this->tauxTVA = $tauxTVA;
    
        return $this;
    }

    /**
     * Get tauxTVA
     *
     * @return float 
     */
    public function getTauxTVA()
    {
        return $this->tauxTVA;
    }

    /**
     * Set compte
     *
     * @param \mgate\TresoBundle\Entity\Compte $compte
     * @return FactureDetail
     */
    public function setCompte(\mgate\TresoBundle\Entity\Compte $compte = null)
    {
        $this->compte = $compte;
    
        return $this;
    }

    /**
     * Get compte
     *
     * @return \mgate\TresoBundle\Entity\Compte 
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * Set factureVente
     *
     * @param \mgate\TresoBundle\Entity\FactureVente $factureVente
     * @return FactureDetail
     */
    public function setFactureVente(\mgate\TresoBundle\Entity\FactureVente $factureVente)
    {
        $this->factureVente = $factureVente;
    
        return $this;
    }

    /**
     * Get factureVente
     *
     * @return \mgate\TresoBundle\Entity\FactureVente 
     */
    public function getFactureVente()
    {
        return $this->factureVente;
    }
}