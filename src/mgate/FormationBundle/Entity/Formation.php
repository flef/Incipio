<?php

namespace mgate\FormationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\FormationBundle\Entity\FormationRepository")
 */
class Formation {

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
     * @ORM\Column(name="categorie", type="array")
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
     * @var mgate\PersonneBundle\Entity\Personne
     *
     * @ORM\ManyToMany(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinTable(name="formation_formateurs")
     */
    private $formateurs;

    /**
     * @var mgate\PersonneBundle\Entity\Personne
     *
     * @ORM\ManyToMany(targetEntity="mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinTable(name="formation_membresPresents")
     */
    private $membresPresents;

    /**
     * @var DateTime
     * @ORM\Column(name="dateDebut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var DateTime
     * @ORM\Column(name="dateFin", type="datetime")
     */
    private $dateFin;

    /**
     * @var string
     * @ORM\Column(name="doc", type="string", length=255, nullable=true)
     */
    private $docPath;

    public static function getCategoriesChoice() {
        return array(
            '0' => 'Junior-Entreprise - Généralité',
            '1' => 'Suivi d\'études',
            '2' => 'Gestion Associative',
            '3' => 'Recrutement Formation Passation',
            '4' => 'Trésorerie',
            '5' => 'Développement Commercial',
            '6' => 'Communication',
            '7' => 'Intervenants',
            '8' => 'Autre',);
    }
    
    public static function getCategoriesChoiceToString($choice = NULL){
        $choices = self::getCategoriesChoice ();
        
        if($choice === NULL)
            return $choices;
        else if (array_key_exists ($choice, $choices))
                return $choices[$choice];
        else return NULL;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set categorie
     *
     * @param integer $categorie
     * @return Formation
     */
    public function setCategorie($categorie) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return integer 
     */
    public function getCategorie() {
        return $this->categorie;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Formation
     */
    public function setTitre($titre) {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre() {
        return $this->titre;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Formation
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set formateurs
     *
     * @param \stdClass $formateurs
     * @return Formation
     */
    public function setFormateurs($formateurs) {
        $this->formateurs = $formateurs;

        return $this;
    }

    /**
     * Get formateurs
     *
     * @return \stdClass 
     */
    public function getFormateurs() {
        return $this->formateurs;
    }

    /**
     * Set membresPresents
     *
     * @param \stdClass $membresPresents
     * @return Formation
     */
    public function setMembresPresents($membresPresents) {
        $this->membresPresents = $membresPresents;

        return $this;
    }

    /**
     * Get membresPresents
     *
     * @return \stdClass 
     */
    public function getMembresPresents() {
        return $this->membresPresents;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->formateurs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->membresPresents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Formation
     */
    public function setDateDebut($dateDebut) {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut() {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Formation
     */
    public function setDateFin($dateFin) {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin() {
        return $this->dateFin;
    }

    /**
     * Set docPath
     *
     * @param string $docPath
     * @return Formation
     */
    public function setDocPath($docPath) {
        $this->docPath = $docPath;

        return $this;
    }

    /**
     * Get docPath
     *
     * @return string 
     */
    public function getDocPath() {
        return $this->docPath;
    }

    /**
     * Add formateurs
     *
     * @param \mgate\PersonneBundle\Entity\Personne $formateurs
     * @return Formation
     */
    public function addFormateur(\mgate\PersonneBundle\Entity\Personne $formateurs) {
        $this->formateurs[] = $formateurs;

        return $this;
    }

    /**
     * Remove formateurs
     *
     * @param \mgate\PersonneBundle\Entity\Personne $formateurs
     */
    public function removeFormateur(\mgate\PersonneBundle\Entity\Personne $formateurs) {
        $this->formateurs->removeElement($formateurs);
    }

    /**
     * Add membresPresents
     *
     * @param \mgate\PersonneBundle\Entity\Personne $membresPresents
     * @return Formation
     */
    public function addMembresPresent(\mgate\PersonneBundle\Entity\Personne $membresPresents) {
        $this->membresPresents[] = $membresPresents;

        return $this;
    }

    /**
     * Remove membresPresents
     *
     * @param \mgate\PersonneBundle\Entity\Personne $membresPresents
     */
    public function removeMembresPresent(\mgate\PersonneBundle\Entity\Personne $membresPresents) {
        $this->membresPresents->removeElement($membresPresents);
    }

}