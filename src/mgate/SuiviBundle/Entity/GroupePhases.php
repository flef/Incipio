<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * mgate\SuiviBundle\Entity\GroupePhases
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GroupePhases
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
     * Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="groupes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $etude;

    /**
     * @var string
     *
     * @ORM\Column(name="string", type="string", length=255)
     */
    private $string;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="smallint")
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity="Phase", mappedBy="groupe", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $phases;


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
     * Set string
     *
     * @param string $string
     * @return GroupePhases
     */
    public function setString($string)
    {
        $this->string = $string;
    
        return $this;
    }

    /**
     * Get string
     *
     * @return string 
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return GroupePhases
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
     * Set description
     *
     * @param string $description
     * @return GroupePhases
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
}
