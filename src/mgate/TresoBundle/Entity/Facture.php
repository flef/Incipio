<?php

namespace mgate\TresoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FV
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FactureVente
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
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;
    
    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;
    
    /**
     * @ORM\OneToMany(targetEntity="FactureDetail", mappedBy="factureVente", cascade={"persist", "merge", "refresh", "remove"})
     */
    private $detailsDeVente;
    

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
        $this->detailsDeVente = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set numero
     *
     * @param integer $numero
     * @return FactureVente
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
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return FactureVente
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add detailsDeVente
     *
     * @param \mgate\TresoBundle\Entity\FactureDetail $detailsDeVente
     * @return FactureVente
     */
    public function addDetailsDeVente(\mgate\TresoBundle\Entity\FactureDetail $detailsDeVente)
    {
        $this->detailsDeVente[] = $detailsDeVente;
    
        return $this;
    }

    /**
     * Remove detailsDeVente
     *
     * @param \mgate\TresoBundle\Entity\FactureDetail $detailsDeVente
     */
    public function removeDetailsDeVente(\mgate\TresoBundle\Entity\FactureDetail $detailsDeVente)
    {
        $this->detailsDeVente->removeElement($detailsDeVente);
    }

    /**
     * Get detailsDeVente
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetailsDeVente()
    {
        return $this->detailsDeVente;
    }
}