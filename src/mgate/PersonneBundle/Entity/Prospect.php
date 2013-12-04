<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \mgate\CommentBundle\Entity;

/**
 * mgate\PersonneBundle\Entity\Prospect
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\ProspectRepository")
 */
class Prospect
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
     * @ORM\OneToMany(targetEntity="Employe", mappedBy="prospect")
     */
    private $employes;

    /**
     * , cascade={"persist"}
     * @ORM\OneToOne(targetEntity="\mgate\CommentBundle\Entity\Thread", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;
    
    /**
     * @var string $nom
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    
    /**
     * @var string $entite
     *
     * @ORM\Column(name="entite", type="integer", nullable=true)
     * @Assert\Choice(callback = "getEntiteChoiceAssert")
     */
    private $entite;
    
    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

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
     * Set thread
     *
     * @param \mgate\CommentBundle\Entity\Thread $thread
     * @return Prospect
     */
    public function setThread(\mgate\CommentBundle\Entity\Thread $thread)
    {
        $this->thread = $thread;
    
        return $this;
    }

    /**
     * Get thread
     *
     * @return mgate\CommentBundle\Entity\Thread 
     */
    public function getThread()
    {
        return $this->thread;
    }
    
    /**
     * Add employes
     *
     * @param mgate\PersonneBundle\Entity\Employe $employes
     * @return Prospect
     */
    public function addEmploye(\mgate\PersonneBundle\Entity\Employe $employes)
    {
        $this->employes[] = $employes;
    
        return $this;
    }

    /**
     * Remove employes
     *
     * @param mgate\PersonneBundle\Entity\Employe $employes
     */
    public function removeEmploye(\mgate\PersonneBundle\Entity\Employe $employes)
    {
        $this->employes->removeElement($employes);
    }

    /**
     * Get employes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEmployes()
    {
        return $this->employes;
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     * @return Prospect
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
     * Constructor
     */
    public function __construct()
    {
        $this->employes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set entite
     *
     * @param string $entite
     * @return Prospect
     */
    public function setEntite($entite)
    {
        $this->entite = $entite;
    
        return $this;
    }

    /**
     * Get entite
     *
     * @return string 
     */
    public function getEntite()
    {
        return $this->entite;
    }
    
    public static function getEntiteChoice()
    {
        return array(   0 => "Autre Type d'Entreprise",
            1 => 'Particulier',
            2 => 'Association',
            3 => 'Start-Up',
            4 => 'Micro-Entreprises (moins de 10 salariés)',
            5 => 'Très Petites Entreprises (moins de 20 salariés)',
            6 => 'Petites et les Moyennes Entreprises (moins de 250 salariés)',
            7 => 'Entreprises de Taille Intermédiaire (moins de 5000 salariés)',
            8 => 'Grandes Entreprises (plus de 5000 salariés)',
            );
    }
    public static function getEntiteChoiceAssert()
    {
        return array_keys(self::getEntiteChoice());
    }
    
    public function getEntiteToString()
    {
        if(!$this->entite) return "";
        $tab = $this->getEntiteChoice();
        return $tab[$this->entite];
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Prospect
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    
        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }
}