<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\DomaineCompetence
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DomaineCompetence
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
     * @ORM\OneToMany(targetEntity="Etude", mappedBy="domaineCompetence")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;
    
    /** nombre de developpeur estimé
     * @var string $nom
     *
     * @ORM\Column(name="nom", type="text", nullable=false)
     */
    private $nom;

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
        $this->etude = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     * @return DomaineCompetence
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Get etude
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEtude()
    {
        return $this->etude;
    }
}