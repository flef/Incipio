<?php

namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * mgate\PersonneBundle\Entity\Poste
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\PosteRepository")
 */
class Poste
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
    private $intitule;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Membre", mappedBy="poste")
        * @ORM\JoinColumn(nullable=true)
     */
    private $membres;



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
     * Get intitule
     *
     * @return string 
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     * @return Poste
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;
    
        return $this;
    }
    
    /**
     * Add membre
     *
     * @param mgate\PersonneBundle\Entity\Membre $membre
     * @return Prospect
     */
    public function addMembre(\mgate\PersonneBundle\Entity\Membre $membre)
    {
        $this->membres[] = $membre;
    
        return $this;
    }

    /**
     * Remove membre
     *
     * @param mgate\PersonneBundle\Entity\Membre $membre
     */
    public function removeMembre(\mgate\PersonneBundle\Entity\Membre $membre)
    {
        $this->membres->removeElement($membre);
    }

    /**
     * Get membres
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMembres()
    {
        return $this->membres;
    }

}