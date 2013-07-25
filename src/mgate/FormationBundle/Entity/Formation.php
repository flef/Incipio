<?php

namespace mgate\FormationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\FormationBundle\Entity\FormationRepository")
 */
class Formation
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
     * @ORM\Column(name="categorie", type="smallint")
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    
    /**
     * @var mgate\PersonneBundle\Entity\Membre
     *
     * @ORM\ManyToMany(targetEntity="mgate\PersonneBundle\Entity\Membre")
     * @ORM\JoinTable(name="formation_formateurs")
     */
    private $formateurs;

    /**
     * @var mgate\PersonneBundle\Entity\Membre
     *
     * @ORM\ManyToMany(targetEntity="mgate\PersonneBundle\Entity\Membre")
     * @ORM\JoinTable(name="formation_membresPresents")
     */
    private $membresPresents;


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
     * Set categorie
     *
     * @param integer $categorie
     * @return Formation
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
     * Set titre
     *
     * @param string $titre
     * @return Formation
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Formation
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
     * Set formateurs
     *
     * @param \stdClass $formateurs
     * @return Formation
     */
    public function setFormateurs($formateurs)
    {
        $this->formateurs = $formateurs;
    
        return $this;
    }

    /**
     * Get formateurs
     *
     * @return \stdClass 
     */
    public function getFormateurs()
    {
        return $this->formateurs;
    }

    /**
     * Set membresPresents
     *
     * @param \stdClass $membresPresents
     * @return Formation
     */
    public function setMembresPresents($membresPresents)
    {
        $this->membresPresents = $membresPresents;
    
        return $this;
    }

    /**
     * Get membresPresents
     *
     * @return \stdClass 
     */
    public function getMembresPresents()
    {
        return $this->membresPresents;
    }
}
