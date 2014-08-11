<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\TresoBundle\Entity\FactureRepository")
 */
class Facture
{
    /* Les factures suivantes ont une TVA renseignée dans facture détail (TVA non global) */
    const TYPE_ACHAT = 1;
    const TYPE_VENTE = 2;
    /*
     * Les factures suivantes ont une TVA global au taux normal
     */
    const TYPE_VENTE_ACCOMPTE = 3;
    const TYPE_VENTE_INTERMEDIAIRE = 4;
    const TYPE_VENTE_SOLDE = 5;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Etude", inversedBy="factures",  cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Prospect", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $beneficiaire;
    
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
     * @var float
     *
     * @ORM\Column(name="tauxTVAglobal", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $tauxTVAglobal;
    
    /**
     * @ORM\OneToMany(targetEntity="FactureDetail", mappedBy="facture", cascade={"persist", "detach", "remove"}, orphanRemoval=true)
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
    
    /*
     * pour la TVA collectée (factures clients), la date d’exigibilité c’est la date d’encaissement
     * pour la TVA déductible, la date d’exigibilité c’est soit la date de facturation dans le cas de vente de biens soit la date de décaissement dans le cas de services
     * la CNJE simplifie pour les Junior-Entrepreneurs en leur disant de prendre en compte la date de facturation pour toutes les opérations (biens et services)
     */
    public function getDate(){
        return $this->type == self::TYPE_ACHAT ? $this->dateEmission : $this->dateVersement;
    }
    
    public function getReference(){
        return $this->exercice.'-'.($this->type > 1 ? 'FV' : 'FA').'-'. sprintf('%1$02d', $this->numero);
    }
    
    public function getMontantHT(){
       $montantHT = 0;
       foreach ($this->details as $detail)
            $montantHT += $detail->getMontantHT();
       return $montantHT;
    }
    
    public function getMontantTVA(){
        $TVA = 0;
        if($this->type > self::TYPE_VENTE){
            foreach ($this->details as $detail) {
                $TVA += $detail->getMontantHT();
            }
            $TVA = $this->tauxTVAglobal / 100 * $TVA;
        }
        else {
            foreach ($this->details as $detail){
                $TVA += $detail->getMontantHT() * $detail->getTauxTVA() / 100;
            }
        }
        return $TVA;
    }
    
    public function getMontantTTC(){
        return $this->getMontantHT() + $this->getMontantTVA();
    }
    
    public function getTypeAbbrToString(){
        $type = array(
            0 => 'Facture',
            1 => 'Facture',
            2 => 'FV',
            3 => \mgate\PubliBundle\Controller\TraitementController::DOCTYPE_FACTURE_ACOMTE,
            4 => \mgate\PubliBundle\Controller\TraitementController::DOCTYPE_FACTURE_INTERMEDIAIRE,
            5 => \mgate\PubliBundle\Controller\TraitementController::DOCTYPE_FACTURE_SOLDE);
        
        return $type[$this->type];
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
            self::TYPE_ACHAT => 'FA - Facture d\'achat',
            self::TYPE_VENTE => 'FV - Facture de vente',
            self::TYPE_VENTE_ACCOMPTE => 'FV - Facture d\'acompte',
            self::TYPE_VENTE_INTERMEDIAIRE => 'FV - Facture intermédiaire',
            self::TYPE_VENTE_SOLDE => 'FV - Facture de solde',
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
        $details->setFacture();
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
    
    /**
     * Set etude
     *
     * @param \mgate\SuiviBundle\Entity\Etude $etude
     * @return BV
     */
    public function setEtude(\mgate\SuiviBundle\Entity\Etude $etude = null)
    {
        $this->etude = $etude;
    
        return $this;
    }

    /**
     * Get etude
     *
     * @return \mgate\SuiviBundle\Entity\Etude 
     */
    public function getEtude()
    {
        return $this->etude;
    }
   
    /**
     * Set beneficiaire
     *
     * @param \mgate\PersonneBundle\Entity\Prospect $beneficiaire
     * @return Facture
     */
    public function setBeneficiaire(\mgate\PersonneBundle\Entity\Prospect $beneficiaire)
    {
        $this->beneficiaire = $beneficiaire;
    
        return $this;
    }

    /**
     * Get beneficiaire
     *
     * @return \mgate\PersonneBundle\Entity\Prospect 
     */
    public function getBeneficiaire()
    {
        return $this->beneficiaire;
    }

    /**
     * Set tauxTVAglobal
     *
     * @param float $tauxTVAglobal
     * @return Facture
     */
    public function setTauxTVAglobal($tauxTVAglobal)
    {
        $this->tauxTVAglobal = $tauxTVAglobal;
    
        return $this;
    }

    /**
     * Get tauxTVAglobal
     *
     * @return float 
     */
    public function getTauxTVAglobal()
    {
        return $this->tauxTVAglobal;
    }
}