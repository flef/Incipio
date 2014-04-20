<?php
namespace mgate\PubliBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RelatedDocument
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
   
    /**
     * @ORM\OneToOne(targetEntity="Document", mappedBy="relation", cascade={"persist", "merge"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $document;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Membre", inversedBy="relatedDocuments", cascade={"persist"})
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id", nullable=true)
     */
    private $membre;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\SuiviBundle\Entity\Etude", inversedBy="relatedDocuments", cascade={"persist"})
     * @ORM\JoinColumn(name="etude_id", referencedColumnName="id", nullable=true)
     */
    private $etude;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\FormationBundle\Entity\Formation", cascade={"persist"})
     * @ORM\JoinColumn(name="formation_id", referencedColumnName="id", nullable=true)
     */
    private $formation;
    
    /**
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Prospect", cascade={"persist"})
     * @ORM\JoinColumn(name="prospect_id", referencedColumnName="id", nullable=true)
     */
    private $prospect;
    
    

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
     * Set document
     *
     * @param \mgate\PubliBundle\Entity\Document $document
     * @return CategorieDocument
     */
    public function setDocument(\mgate\PubliBundle\Entity\Document $document = null)
    {
        $this->document = $document;
    
        return $this;
    }

    /**
     * Get document
     *
     * @return \mgate\PubliBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set membre
     *
     * @param \mgate\PersonneBundle\Entity\Membre $membre
     * @return CategorieDocument
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

    /**
     * Set etude
     *
     * @param \mgate\SuiviBundle\Entity\Etude $etude
     * @return CategorieDocument
     */
    public function setEtude(\mgate\SuiviBundle\Entity\Etude $etude = null)
    {
        $this->etude = $etude;
    
        return $this;
    }

    /**
     * Get etude
     *
     * @return \mgate\SuiviBundle\Entity\Etude 
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set formation
     *
     * @param \mgate\FormationBundle\Entity\Formation $formation
     * @return CategorieDocument
     */
    public function setFormation(\mgate\FormationBundle\Entity\Formation $formation = null)
    {
        $this->formation = $formation;
    
        return $this;
    }

    /**
     * Get formation
     *
     * @return \mgate\FormationBundle\Entity\Formation 
     */
    public function getFormation()
    {
        return $this->formation;
    }

    /**
     * Set prospect
     *
     * @param \mgate\PersonneBundle\Entity\Prospect $prospect
     * @return CategorieDocument
     */
    public function setProspect(\mgate\PersonneBundle\Entity\Prospect $prospect = null)
    {
        $this->prospect = $prospect;
    
        return $this;
    }

    /**
     * Get prospect
     *
     * @return \mgate\PersonneBundle\Entity\Prospect 
     */
    public function getProspect()
    {
        return $this->prospect;
    }
}