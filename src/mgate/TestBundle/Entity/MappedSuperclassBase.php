<?php

namespace mgate\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;

//ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")

/** 
 * @ORM\MappedSuperclass
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class MappedSuperclassBase
{
    //protected $id;
    /**
     * @ORM\OneToOne(targetEntity="mgate\TestBundle\Entity\TestThread",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $thread;
    
    /** @ORM\Column(type="integer") */
    protected $mapped1;
    /** @ORM\Column(type="string") */
    protected $mapped2;
    
    /**
     * ORM\ManyToOne(targetEntity="Relatedclass", inversedBy="Subclasss", cascade={"persist"})
     * ORM\JoinColumn()
     */
    protected $mappedRelated1; //JoinColumn(name="related1_id", referencedColumnName="id")

    // ... more fields and methods
    
    
   /**
     * @param string $champ1
     * @return Subclass
     */
    public function setMapped1($mapped1)
    {
        $this->mapped1 = $mapped1;
        return $this;
    }

    /**
     * @return string 
     */
    public function getMapped1()
    {
        return $this->mapped1;
    }
    
   /**
     * @param string $champ1
     * @return Subclass
     */
    public function setMapped2($mapped2)
    {
        $this->mapped2 = $mapped2;
        return $this;
    }

    /**
     * @return string 
     */
    public function getMapped2()
    {
        return $this->mapped2;
    }
    
    
   /**
     * @param mgate\TestBundle\Entity\Relatedclass $mappedRelated1
     * @return MappedSuperclassBase
     */
    public function setMappedRelated1($mappedRelated1)
    {
        $this->mappedRelated1 = $mappedRelated1;
        return $this;
    }

    /**
     * @return mgate\TestBundle\Entity\Relatedclass
     */
    public function getMappedRelated1()
    {
        return $this->mappedRelated1;
    }

    
 

    /**
     * Set thread
     *
     * @param mgate\TestBundle\Entity\TestThread $thread
     * @return MappedSuperclassBase
     */
    public function setThread(\mgate\TestBundle\Entity\TestThread $thread)
    {
        $this->thread = $thread;
    
        return $this;
    }

    /**
     * Get thread
     *
     * @return mgate\TestBundle\Entity\TestThread 
     */
    public function getThread()
    {
        return $this->thread;
    }
}