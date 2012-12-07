<?php

namespace mgate\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;

/**
 * mgate\TestBundle\Entity\Subclass
 *
 * @ORM\Table()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Entity(repositoryClass="mgate\TestBundle\Entity\SubclassRepository")
 */
class Subclass extends MappedSuperclassBase
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id; // doit-etre en protected pour Comment Bundle

    /**
     * @var string $champ1
     *
     * @ORM\Column(name="champ1", type="string", length=255)
     */
    private $champ1;
    
    /**
     * @ORM\ManyToOne(targetEntity="Relatedclass", inversedBy="Subclasss", cascade={"persist"})
     * @ORM\JoinColumn()
     */
    protected $mappedRelated1; //JoinColumn(name="related1_id", referencedColumnName="id")


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
}
