<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactureDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class FactureDetail
{
    /* @abstract dans le cas d'une facture de vente, on applique pas de TVA sur chaque ligne de la facture (mode normal),
     * la TVA est appliqué au taux normal sur le total HT de la facture. 
     * Cela permet notamment de prendre en compte correctement les déductions des factures d'acompte sans faire de déduction de TVA.
     * Dans le cas d'une refacturation, il est possible de renseigner une TVA en appliquant le Type TYPE_AUTRE_TVA. Type par défaut dans le cas d'une facture d'achat.
     */
    const TYPE_PRESTATION_HT    = 1;
    const TYPE_FRAIS_HT         = 2;
    const TYPE_DEDUCTION_HT     = 3;
    const TYPE_AUTRE_TVA        = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $facture;

    /**
     * @var integer
     * @abstract 1 is Deduction, > 2 is vente (prestation, frais, refacturation)
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;
    

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="nombreJEH", type="integer", nullable=true)
     */
    private $nombreJEH;

    /**
     * @var float
     *
     * @ORM\Column(name="prixJEH", type="decimal", precision=6, scale=2, nullable=true)
     * 
     */
    private $prixJEH;

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
     * ADDITIONAL
     */

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function calculateMontantHT(){
        switch($this->type){
            case self::TYPE_DEDUCTION_HT :
                $this->montantHT = - abs($this->montantHT);
                $this->tauxTVA = 0;        
                break;
            case self::TYPE_PRESTATION_HT :
                $this->montantHT = $this->nombreJEH * $this->prixJEH;
                $this->tauxTVA = 0;
                break;
            case self::TYPE_FRAIS_HT :
                $this->tauxTVA = 0;
                break;
            default:
                break;
        }
    }
    
    public function getMontantTVA(){
        if($this->type == self::TYPE_AUTRE_TVA)
            return $this->tauxTVA * $this->montantHT / 100;
        else
            return 0;    
    }
    
    public function getMontantTTC(){
        return $this->montantHT + $this->getMontantTVA();
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getTypeToString()
    {
        $type = $this->getTypeChoices();
        return $type[$this->type];
    }
    
    public static function getTypeChoices(){
        return array(
            self::TYPE_DEDUCTION_HT => 'Déduction (TVA globale au taux normal)',
            self::TYPE_PRESTATION_HT => 'Préstation (TVA globale au taux normal)',
            self::TYPE_FRAIS_HT => 'Frais (TVA globale au taux normal)',
            self::TYPE_AUTRE_TVA => 'Autre',
            );
    } 
    


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
     * Set facture
     *
     * @param \mgate\TresoBundle\Entity\Facture $facture
     * @return FactureDetail
     */
    public function setFacture(\mgate\TresoBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;
    
        return $this;
    }

    /**
     * Get facture
     *
     * @return \mgate\TresoBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set nombreJEH
     *
     * @param integer $nombreJEH
     * @return FactureDetail
     */
    public function setNombreJEH($nombreJEH)
    {
        $this->nombreJEH = $nombreJEH;
    
        return $this;
    }

    /**
     * Get nombreJEH
     *
     * @return integer 
     */
    public function getNombreJEH()
    {
        return $this->nombreJEH;
    }

    /**
     * Set prixJEH
     *
     * @param string $prixJEH
     * @return FactureDetail
     */
    public function setPrixJEH($prixJEH)
    {
        $this->prixJEH = $prixJEH;
    
        return $this;
    }

    /**
     * Get prixJEH
     *
     * @return string 
     */
    public function getPrixJEH()
    {
        return $this->prixJEH;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return FactureDetail
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}