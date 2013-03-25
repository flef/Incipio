<?php

namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * mgate\SuiviBundle\Entity\ClientContact
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\SuiviBundle\Entity\ClientContactRepository")
 */
class ClientContact
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
     * @ORM\ManyToOne(targetEntity="Etude", inversedBy="clientContacts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etude;

    /** , inversedBy="clientContacts", cascade={"persist"}
     * @ORM\ManyToOne(targetEntity="mgate\PersonneBundle\Entity\Personne")
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
     * @ORM\OneToOne(targetEntity="\mgate\CommentBundle\Entity\Thread",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;
    
    /**
     * @var string $contenu
     * @ORM\Column(name="contenu", type="text",nullable=true)
     */
    private $contenu;
    
    /**
     * @var text $moyenContact
     * @ORM\Column(name="moyenContact", type="text",nullable=true)
     */
    private $moyenContact;

    public function __construct() {
        $this->date = new \DateTime('now');
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
     * Set etude
     *
     * @param string $etude
     * @return ClientContact
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
     * @return ClientContact
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
     * @return ClientContact
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
     * @return ClientContact
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

    /**
     * Set mail
     *
     * @param boolean $mail
     * @return ClientContact
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    
        return $this;
    }

    /**
     * Get mail
     *
     * @return boolean 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set appel
     *
     * @param boolean $appel
     * @return ClientContact
     */
    public function setAppel($appel)
    {
        $this->appel = $appel;
    
        return $this;
    }

    /**
     * Get appel
     *
     * @return boolean 
     */
    public function getAppel()
    {
        return $this->appel;
    }

    /**
     * Set moyenContact
     *
     * @param string $moyenContact
     * @return ClientContact
     */
    public function setMoyenContact($moyenContact)
    {
        $this->moyenContact = $moyenContact;
    
        return $this;
    }

    /**
     * Get moyenContact
     *
     * @return string 
     */
    public function getMoyenContact()
    {
        return $this->moyenContact;
    }
}