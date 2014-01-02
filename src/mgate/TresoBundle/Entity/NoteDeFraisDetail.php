<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NoteDeFraisDetail
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class NoteDeFraisDetail
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
     * @ORM\ManyToOne(targetEntity="NoteDeFrais", inversedBy="details", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $noteDeFrais;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="prixHT", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $prixHT;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxTVA", type="decimal", precision=6, scale=2, nullable=true)
     */
    private $tauxTVA;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="kilometrage", type="integer", nullable=true)
     */
    private $kilometrage;

    /**
     * @var float
     *
     * @ORM\Column(name="tauxKm", type="integer", nullable=true)
     */
    private $tauxKm;

    
    //categorie à ajouter via ManytoMany compteComptable
    
    // Perso
    public static function getTypeChoices(){
        return array(1 => 'Classique',
            2 => 'Kilométrique',);
    } 
    
    // Getter Setter Auto Generated

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
     * @return NoteDeFraisDetail
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
     * Set prixHT
     *
     * @param float $prixHT
     * @return NoteDeFraisDetail
     */
    public function setPrixHT($prixHT)
    {
        $this->prixHT = $prixHT;
    
        return $this;
    }

    /**
     * Get prixHT
     *
     * @return float 
     */
    public function getPrixHT()
    {
        return $this->prixHT;
    }

    /**
     * Set tauxTVA
     *
     * @param float $tauxTVA
     * @return NoteDeFraisDetail
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
     * Set type
     *
     * @param integer $type
     * @return NoteDeFraisDetail
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
     * Set kilometrage
     *
     * @param integer $kilometrage
     * @return NoteDeFraisDetail
     */
    public function setKilometrage($kilometrage)
    {
        $this->kilometrage = $kilometrage;
    
        return $this;
    }

    /**
     * Get kilometrage
     *
     * @return integer 
     */
    public function getKilometrage()
    {
        return $this->kilometrage;
    }

    /**
     * Set tauxKm
     *
     * @param float $tauxKm
     * @return NoteDeFraisDetail
     */
    public function setTauxKm($tauxKm)
    {
        $this->tauxKm = $tauxKm;
    
        return $this;
    }

    /**
     * Get tauxKm
     *
     * @return float 
     */
    public function getTauxKm()
    {
        return $this->tauxKm;
    }

    /**
     * Set categorie
     *
     * @param integer $categorie
     * @return NoteDeFraisDetail
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    
        return $this;
    }

    /**
     * Get categorie
     *
     * @return integer 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set noteDeFrais
     *
     * @param \mgate\TresoBundle\Entity\NoteDeFrais $noteDeFrais
     * @return NoteDeFraisDetail
     */
    public function setNoteDeFrais(\mgate\TresoBundle\Entity\NoteDeFrais $noteDeFrais = null)
    {
        $this->noteDeFrais = $noteDeFrais;
    
        return $this;
    }

    /**
     * Get noteDeFrais
     *
     * @return \mgate\TresoBundle\Entity\NoteDeFrais 
     */
    public function getNoteDeFrais()
    {
        return $this->noteDeFrais;
    }
}