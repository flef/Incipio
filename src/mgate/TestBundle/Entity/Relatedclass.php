<?php

namespace mgate\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\TestBundle\Entity\Relatedclass
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\TestBundle\Entity\SubclassRepository")
 */
class Relatedclass
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
     * @var string $champ1
     *
     * @ORM\Column(name="champ1", type="string", length=255)
     */
    private $champ1;

    
    /**
     * @ORM\OneToMany(targetEntity="mgate\TestBundle\Entity\Subclass", mappedBy="mappedRelated1")
     */
    private $Subclasss; // Ici commentaires prend un "s", car un article a plusieurs commentaires !
    

    public function __construct()
    {
        // Rappelez-vous, on a un attribut qui doit contenir un ArrayCollection, on doit l'initialiser dans le constructeur
        $this->Subclasss = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set champ1
     *
     * @param string $champ1
     * @return Subclass
     */
    public function setChamp1($champ1)
    {
        $this->champ1 = $champ1;
    
        return $this;
    }

    /**
     * Get champ1
     *
     * @return string 
     */
    public function getChamp1()
    {
        return $this->champ1;
    }
    
    
    public function addSubclass(\mgate\TestBundle\Entity\Subclass $sub)
    {
        $this->Subclasss[] = $sub;
        return $this;
    }

    public function removeSubclass(\mgate\TestBundle\Entity\Subclass $sub)
    {
        $this->Subclasss->removeElement($sub);
    }

    public function getSubclasss()
    {
        return $this->Subclasss;
    }
}
