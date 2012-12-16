<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="entite", type="string", length=255)
     */
    private $entite;
    
    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;
    
    
    /**
     * @var string $signataire_titre
     *
     * @ORM\Column(name="signataire_titre", type="string", length=255)
     */
    private $signataire_titre;
    
    /**
     * @var string $signataire_fonction
     *
     * @ORM\Column(name="signataire_fonction", type="string", length=255)
     */
    private $signataire_fonction;
    
    /**
     * @var string $signataire_nom
     *
     * @ORM\Column(name="signataire_nom", type="string", length=255)
     */
    private $signataire_nom;
    
    /**
     * @var string $signataire_prenom
     *
     * @ORM\Column(name="signataire_prenom", type="string", length=255)
     */
    private $signataire_prenom;


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

    /**
     * Set signataire_titre
     *
     * @param string $signataireTitre
     * @return Prospect
     */
    public function setSignataireTitre($signataireTitre)
    {
        $this->signataire_titre = $signataireTitre;
    
        return $this;
    }

    /**
     * Get signataire_titre
     *
     * @return string 
     */
    public function getSignataireTitre()
    {
        return $this->signataire_titre;
    }

    /**
     * Set signataire_fonction
     *
     * @param string $signataireFonction
     * @return Prospect
     */
    public function setSignataireFonction($signataireFonction)
    {
        $this->signataire_fonction = $signataireFonction;
    
        return $this;
    }

    /**
     * Get signataire_fonction
     *
     * @return string 
     */
    public function getSignataireFonction()
    {
        return $this->signataire_fonction;
    }

    /**
     * Set signataire_nom
     *
     * @param string $signataireNom
     * @return Prospect
     */
    public function setSignataireNom($signataireNom)
    {
        $this->signataire_nom = $signataireNom;
    
        return $this;
    }

    /**
     * Get signataire_nom
     *
     * @return string 
     */
    public function getSignataireNom()
    {
        return $this->signataire_nom;
    }

    /**
     * Set signataire_prenom
     *
     * @param string $signatairePrenom
     * @return Prospect
     */
    public function setSignatairePrenom($signatairePrenom)
    {
        $this->signataire_prenom = $signatairePrenom;
    
        return $this;
    }

    /**
     * Get signataire_prenom
     *
     * @return string 
     */
    public function getSignatairePrenom()
    {
        return $this->signataire_prenom;
    }
}