<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * mgate\PersonneBundle\Entity\Personne
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\PersonneRepository")
 */
class Personne
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string $prenom
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;
    
    /**
     * @var string $nom
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    
    /**
     * @var string $sexe
     *
     * @ORM\Column(name="sexe", type="string", length=255)
     */
    private $sexe;
    
    /**
     * @var string $poste
     *
     * @ORM\Column(name="poste", type="string", length=255)
     */
    private $poste;

    /**
     * @var string $mobile
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @var string $fix
     *
     * @ORM\Column(name="fix", type="string", length=255, nullable=true)
     */
    private $fix;

    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;
    
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;
    
    
    /**
     * @ORM\OneToOne(targetEntity="mgate\PersonneBundle\Entity\Employe", mappedBy="personne")
     * @ORM\JoinColumn(nullable=true)
     */
    private $employe;
    
    /**
     * @ORM\OneToOne(targetEntity="mgate\UserBundle\Entity\User", mappedBy="personne")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;
    
    /**
     * @ORM\OneToOne(targetEntity="mgate\PersonneBundle\Entity\Membre", mappedBy="membre")
     * @ORM\JoinColumn(nullable=true)
     */
    private $membre;

    // pour afficher Prénom Nom
    // Merci de ne pas supprimer
    public function getPrenomNom()
    {
        return $this->prenom.' '.$this->nom;
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
     * Set prenom
     *
     * @param string $prenom
     * @return Personne
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    
        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Personne
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
     * Set sexe
     *
     * @param string $sexe
     * @return Personne
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;
    
        return $this;
    }

    /**
     * Get sexe
     *
     * @return string 
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set poste
     *
     * @param string $poste
     * @return Personne
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;
    
        return $this;
    }

    /**
     * Get poste
     *
     * @return string 
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Personne
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set fix
     *
     * @param string $fix
     * @return Personne
     */
    public function setFix($fix)
    {
        $this->fix = $fix;
    
        return $this;
    }

    /**
     * Get fix
     *
     * @return string 
     */
    public function getFix()
    {
        return $this->fix;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Personne
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
     * Set email
     *
     * @param string $email
     * @return Personne
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set employe
     *
     * @param \mgate\PersonneBundle\Entity\Employe $employe
     * @return Personne
     */
    public function setEmploye(\mgate\PersonneBundle\Entity\Employe $employe = null)
    {
        $this->employe = $employe;
    
        return $this;
    }

    /**
     * Get employe
     *
     * @return \mgate\PersonneBundle\Entity\Employe 
     */
    public function getEmploye()
    {
        return $this->employe;
    }

    /**
     * Set user
     *
     * @param \mgate\UserBundle\Entity\User $user
     * @return Personne
     */
    public function setUser(\mgate\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \mgate\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set membre
     *
     * @param \mgate\PersonneBundle\Entity\Membre $membre
     * @return Personne
     */
    public function setMembre(\mgate\PersonneBundle\Entity\Membre $membre = null)
    {
        $this->membre = $membre;
    
        return $this;
    }

    /**
     * Get membre
     *
     * @return \mgate\PersonneBundle\Entity\Membre 
     */
    public function getMembre()
    {
        return $this->membre;
    }
}