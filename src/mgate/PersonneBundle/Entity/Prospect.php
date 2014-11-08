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


namespace mgate\PersonneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \mgate\CommentBundle\Entity;

/**
 * mgate\PersonneBundle\Entity\Prospect
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="mgate\PersonneBundle\Entity\ProspectRepository")
 */
class Prospect
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
     * @ORM\OneToMany(targetEntity="Employe", mappedBy="prospect")
     */
    private $employes;

    /**
     * , cascade={"persist"}
     * @ORM\OneToOne(targetEntity="\mgate\CommentBundle\Entity\Thread", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $thread;
    
    /**
     * @var string $nom
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    
    /**
     * @var string $entite
     *
     * @ORM\Column(name="entite", type="integer", nullable=true)
     * @Assert\Choice(callback = "getEntiteChoiceAssert")
     */
    private $entite;
    
    /**
     * @var string $adresse
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

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
     * Add employes
     *
     * @param mgate\PersonneBundle\Entity\Employe $employes
     * @return Prospect
     */
    public function addEmploye(\mgate\PersonneBundle\Entity\Employe $employes)
    {
        $this->employes[] = $employes;
    
        return $this;
    }

    /**
     * Remove employes
     *
     * @param mgate\PersonneBundle\Entity\Employe $employes
     */
    public function removeEmploye(\mgate\PersonneBundle\Entity\Employe $employes)
    {
        $this->employes->removeElement($employes);
    }

    /**
     * Get employes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEmployes()
    {
        return $this->employes;
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     * @return Prospect
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set entite
     *
     * @param string $entite
     * @return Prospect
     */
    public function setEntite($entite)
    {
        $this->entite = $entite;
    
        return $this;
    }

    /**
     * Get entite
     *
     * @return string 
     */
    public function getEntite()
    {
        return $this->entite;
    }
    
    public static function getEntiteChoice()
    {
        return array(   
            1 => 'Particulier',
            2 => 'Association',
            3 => 'TPE (moins de 20 salariés)',
            4 => 'PME / ETI (plus de 20 salariés)',
            5 => 'Grand Groupe',
            6 => 'Ecole',
            7 => "Administration",
            8 => "Junior-Entreprise",
            );
    }
    public static function getEntiteChoiceAssert()
    {
        return array_keys(self::getEntiteChoice());
    }
    
    public function getEntiteToString()
    {
        if(!$this->entite) return "";
        $tab = $this->getEntiteChoice();
        return $tab[$this->entite];
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Prospect
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    
        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }
}