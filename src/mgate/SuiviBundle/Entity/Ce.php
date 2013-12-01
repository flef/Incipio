<?php

namespace mgate\SuiviBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * mgate\SuiviBundle\Entity\Ce
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ce extends DocType
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
     * @ORM\OneToOne(targetEntity="mgate\PersonneBundle\Entity\Membre", inversedBy="conventionEleve")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    protected $membre;
    
    
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
     * Set membre
     *
     * @param mgate\PersonneBundle\Entity\Membre $membre
     * @return Ce
     */
    public function setEtude(\mgate\PersonneBundle\Entity\Membre $membre = null)
    {
        $this->membre = $membre;    
        return $this;
    }

    /**
     * Get membre
     *
     * @return mgate\PersonneBundle\Entity\Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }
}