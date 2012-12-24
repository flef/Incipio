<?php

namespace mgate\SuiviBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\Ap
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\ApRepository")
 */
class Ap extends DocType
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="aps", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /**
     * @var integer $fraisDossier
     *
     * @ORM\Column(name="fraisDossier", type="integer")
     */
    private $fraisDossier;

    /**
     * @var text $capacitedev
     *
     * @ORM\Column(name="capacitedev", type="text",nullable=true)
     */
    private $capacitedev;
    
     /**
     * @var text $presentationprojet
     *
     * @ORM\Column(name="presentationprojet", type="text",nullable=true)
     */
    private $presentationprojet;
    
     /**
     * @var text $descriptionprestation
     *
     * @ORM\Column(name="descriptionprestation", type="text",nullable=true)
     */
    private $descriptionprestation;
    
     /**
     * @var integer $typePrestation
     *
     * @ORM\Column(name="validation", type="integer", nullable=true)
     * @Assert\Choice(callback = "getValidationChoiceAssert")
     */
    private $typePrestation;
    

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
     * Set fraisDossier
     *
     * @param integer $fraisDossier
     * @return Ap
     */
    public function setFraisDossier($fraisDossier)
    {
        $this->fraisDossier = $fraisDossier;
    
        return $this;
    }

    /**
     * Get fraisDossier
     *
     * @return integer 
     */
    public function getFraisDossier()
    {
        return $this->fraisDossier;
    }

    /**
     * Set etude
     *
     * @param mgate\SuiviBundle\Entity\Etude $etude
     * @return Ap
     */
    public function setEtude(\mgate\SuiviBundle\Entity\Etude $etude)
    {
        $this->etude = $etude;
    
        return $this;
    }

    /**
     * Get etude
     *
     * @return mgate\SuiviBundle\Entity\Etude 
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set capacitedev
     *
     * @param string $capacitedev
     * @return Ap
     */
    public function setCapacitedev($capacitedev)
    {
        $this->capacitedev = $capacitedev;
    
        return $this;
    }

    /**
     * Get capacitedev
     *
     * @return string 
     */
    public function getCapacitedev()
    {
        return $this->capacitedev;
    }

    /**
     * Set presentationprojet
     *
     * @param string $presentationprojet
     * @return Ap
     */
    public function setPresentationprojet($presentationprojet)
    {
        $this->presentationprojet = $presentationprojet;
    
        return $this;
    }

    /**
     * Get presentationprojet
     *
     * @return string 
     */
    public function getPresentationprojet()
    {
        return $this->presentationprojet;
    }

    /**
     * Set descriptionprestation
     *
     * @param string $descriptionprestation
     * @return Ap
     */
    public function setDescriptionprestation($descriptionprestation)
    {
        $this->descriptionprestation = $descriptionprestation;
    
        return $this;
    }

    /**
     * Get descriptionprestation
     *
     * @return string 
     */
    public function getDescriptionprestation()
    {
        return $this->descriptionprestation;
    }

    /**
     * Set typePrestation
     *
     * @param integer $typePrestation
     * @return Ap
     */
    public function setTypePrestation($typePrestation)
    {
        $this->typePrestation = $typePrestation;
    
        return $this;
    }

    /**
     * Get typePrestation
     *
     * @return integer 
     */
    public function getTypePrestation()
    {
        return $this->typePrestation;
    }
    
    public static function getValidationChoice()
    {
        return array( 0 => "ingénieur informatique", 1 => "ingénieur électronique", 2 => "ingénieur informatique et électronique", 3=>"ingénieur microélectronique");
    }
    public static function getValidationChoiceAssert()
    {
        return array_keys(Ap::getValidationChoice());
    }
}