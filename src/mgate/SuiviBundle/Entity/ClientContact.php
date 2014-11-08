<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace mgate\SuiviBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * mgate\SuiviBundle\Entity\ClientContact
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\Column(name="objet", type="text",nullable=true)
     */
    private $objet;
    
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

    /**
     * Set objet
     *
     * @param string $objet
     * @return ClientContact
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;
    
        return $this;
    }

    /**
     * Get objet
     *
     * @return string 
     */
    public function getObjet()
    {
        return $this->objet;
    }
}