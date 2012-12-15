<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \mgate\CommentBundle\Entity;

/**
 * mgate\SuiviBundle\Entity\Candidature
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\CandidatureRepository")
 */
class Candidature
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="candidatures", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /** , inversedBy="candidatures", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $candidat;

    /**
     * @var boolean $entretenu
     *
     * @ORM\Column(name="entretenu", type="boolean")
     */
    private $entretenu;

    /**
     * @ORM\OneToOne(targetEntity="\mgate\CommentBundle\Entity\Thread",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $thread;

    /**
     * @var boolean $retenu
     *
     * @ORM\Column(name="retenu", type="boolean")
     */
    private $retenu;


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
     * Set etude
     *
     * @param string $etude
     * @return Candidature
     */
    public function setEtude($etude)
    {
        $this->etude = $etude;
    
        return $this;
    }

    /**
     * Get etude
     *
     * @return string 
     */
    public function getEtude()
    {
        return $this->etude;
    }

    /**
     * Set candidat
     *
     * @param mgate\PersonneBundle\Entity\User $candidat
     * @return Candidature
     */
    public function setCandidat(\mgate\PersonneBundle\Entity\User $candidat)
    {
        $this->candidat = $candidat;
    
        return $this;
    }

    /**
     * Get candidat
     *
     * @return mgate\PersonneBundle\Entity\User 
     */
    public function getCandidat()
    {
        return $this->candidat;
    }

    /**
     * Set entretenu
     *
     * @param boolean $entretenu
     * @return Candidature
     */
    public function setEntretenu($entretenu)
    {
        $this->entretenu = $entretenu;
    
        return $this;
    }

    /**
     * Get entretenu
     *
     * @return boolean 
     */
    public function getEntretenu()
    {
        return $this->entretenu;
    }

    /**
     * Set thread
     *
     * @param \mgate\CommentBundle\Entity\Thread $thread
     * @return Prospect
     */
    public function setThread(\mgate\CommentBundle\Entity\Thread $thread)
    {
        $this->thread = $thread;
    
        return $this;
    }

    /**
     * Get thread
     *
     * @return mgate\CommentBundle\Entity\Thread 
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set retenu
     *
     * @param boolean $retenu
     * @return Candidature
     */
    public function setRetenu($retenu)
    {
        $this->retenu = $retenu;
    
        return $this;
    }

    /**
     * Get retenu
     *
     * @return boolean 
     */
    public function getRetenu()
    {
        return $this->retenu;
    }
}