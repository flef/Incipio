<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FV
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Facture
{
    public static $TYPE_ACHAT = 1;
    public static $TYPE_VENTE = 2;
    public static $TYPE_VENTE_ACCOMPTE = 3;
    public static $TYPE_VENTE_INTERMEDIAIRE = 4;
    public static $TYPE_VENTE_SOLDE = 5;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="exercice", type="smallint")
     */
    private $exercice;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="smallint")
     */
    private $numero;

    /**
     * @var integer
     * @abstract 1 is Achat, > 2 is vente
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;
    
    /**
     * @var \DateTime $dateEmission
     *
     * @ORM\Column(name="dateEmission", type="date", nullable=false)
     */
    private $dateEmission;
    
    /**
     * @var \DateTime $dateVersement
     *
     * @ORM\Column(name="dateVersement", type="date", nullable=true)
     */
    private $dateVersement;
    
    /**
     * @ORM\OneToMany(targetEntity="FactureDetail", mappedBy="facture", cascade={"persist", "merge", "refresh", "remove"})
     */
    private $details;
    
    /**
     * @ORM\Column(name="objet", type="text", nullable=false)
     * @var string
     */
    private $objet;
    
    /**
     * ADDITIONNAL
     */
    public function getReference(){
        return '[M-GaTE]'.$this->exercice.'-'.($this->type > 1 ? 'FV' : 'FA').'-'. sprintf('%1$02d', $this->numero);
    }
    
    public function getMontantHT(){
       $montantHT = 0;
       foreach ($this->details as $detail)
            $montantHT += $detail->getMontantHT();           
       return $montantHT;
    }
    
    public function getMontantTVA(){
        $TVA = 0;
        foreach ($this->details as $detail)
            $TVA += $detail->getMontantHT() * $detail->getTauxTVA() / 100;
       return $TVA;
    }
    
    public function getMontantTTC(){
        return $this->getMontantHT() + $this->getMontantTVA();
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
            self::$TYPE_ACHAT => 'FA - Facture d\'achat',
            self::$TYPE_VENTE => 'FV - Facture de vente',
            self::$TYPE_VENTE_ACCOMPTE => 'FV - Facture d\'accomtpe',
            self::$TYPE_VENTE_INTERMEDIAIRE => 'FV - Facture intermédiaire',
            self::$TYPE_VENTE_SOLDE => 'FV - Facture de solde',
            );
    } 
    
    
    /**
     * STANDARDS GETTER / SETTER
     */

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
     * Constructor
     */
    public function __construct()
    {
        $this->details = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set exercice
     *
     * @param integer $exercice
     * @return Facture
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

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Facture
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    
        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Facture
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
    


    /**
     * Set dateEmission
     *
     * @param \DateTime $dateEmission
     * @return Facture
     */
    public function setDateEmission($dateEmission)
    {
        $this->dateEmission = $dateEmission;
    
        return $this;
    }

    /**
     * Get dateEmission
     *
     * @return \DateTime 
     */
    public function getDateEmission()
    {
        return $this->dateEmission;
    }
    
    /**
     * Set dateVersement
     *
     * @param \DateTime $dateVersement
     * @return Facture
     */
    public function setDateVersement($dateVersement)
    {
        $this->dateVersement = $dateVersement;
    
        return $this;
    }

    /**
     * Get dateVersement
     *
     * @return \DateTime 
     */
    public function getDateVersement()
    {
        return $this->dateVersement;
    }

    /**
     * Add details
     *
     * @param \mgate\TresoBundle\Entity\FactureDetail $details
     * @return Facture
     */
    public function addDetail(\mgate\TresoBundle\Entity\FactureDetail $details)
    {
        $this->details[] = $details;
    
        return $this;
    }

    /**
     * Remove details
     *
     * @param \mgate\TresoBundle\Entity\FactureDetail $details
     */
    public function removeDetail(\mgate\TresoBundle\Entity\FactureDetail $details)
    {
        $this->details->removeElement($details);
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetails()
    {
        return $this->details;
    }
    
    /**
     * Set objet
     *
     * @param string $objet
     * @return NoteDeFrais
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;
    
        return $this;
    }

    /**
     * Get objet
     *
     * @return string 
     */
    public function getObjet()
    {
        return $this->objet;
    }
}