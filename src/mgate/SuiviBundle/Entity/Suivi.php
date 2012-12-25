<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use \mgate\CommentBundle\Entity;

/**
 * mgate\SuiviBundle\Entity\Suivi
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\SuiviRepository")
 */
class Suivi
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="suivis", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /** , inversedBy="suivi", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="\mgate\PersonneBundle\Entity\Personne")
     * @ORM\JoinColumn(nullable=false)
     */
    private $faitPar;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var string $contenu
     * @ORM\Column(name="contenu", type="text",nullable=true)
     */
    private $contenu;
    

    /**
     * @ORM\OneToOne(targetEntity="\mgate\CommentBundle\Entity\Thread",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;


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
     * @return Suivi
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
     * Set faitPar
     *
     * @param mgate\PersonneBundle\Entity\Personne $faitPar
     * @return Suivi
     */
    public function setFaitPar(\mgate\PersonneBundle\Entity\Personne $faitPar)
    {
        $this->faitPar = $faitPar;
    
        return $this;
    }

    /**
     * Get faitPar
     *
     * @return mgate\PersonneBundle\Entity\Personne 
     */
    public function getFaitPar()
    {
        return $this->faitPar;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Suivi
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
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
     * Set contenu
     *
     * @param string $contenu
     * @return Suivi
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    
        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }
}