<?php
// src/mgate/UserBundle/Entity/User.php

namespace mgate\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Yaml\Parser; 

/**
 * @ORM\Entity(repositoryClass="mgate\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="\mgate\PersonneBundle\Entity\Personne", inversedBy="user", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $personne;


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
     * Set personne
     *
     * @param \mgate\PersonneBundle\Entity\Personne $personne
     * @return User
     */
    public function setPersonne(\mgate\PersonneBundle\Entity\Personne $personne = null)
    {        
        $this->personne = $personne;
        
        if($personne)
            $this->personne->setUser($this);
     
        return $this;
    }

    /**
     * Get personne
     *
     * @return \mgate\PersonneBundle\Entity\Personne 
     */
    public function getPersonne()
    {
        return $this->personne;
    }
    
    
    static function getRolesNames()
    {
        $pathToSecurity = __DIR__ . '/../../../..' . '/app/config/security.yml';
        $yaml = new Parser();
        $rolesArray = $yaml->parse(file_get_contents($pathToSecurity));
        $arrayKeys = array();
        foreach ($rolesArray['security']['role_hierarchy'] as $key => $value)
        {
            //never allow assigning super admin
            if ($key != 'ROLE_SUPER_ADMIN')
                $arrayKeys[$key] = User::convertRoleToLabel($key);
            //skip values that are arrays --- roles with multiple sub-roles
            if (!is_array($value))
                if ($value != 'ROLE_SUPER_ADMIN')
                    $arrayKeys[$value] = User::convertRoleToLabel($value);
        }
        //sort for display purposes
        asort($arrayKeys);
        return $arrayKeys;
    }

    static private function convertRoleToLabel($role)
    {
        $roleDisplay = str_replace('ROLE_', '', $role);
        $roleDisplay = str_replace('_', ' ', $roleDisplay);
        return ucwords(strtolower($roleDisplay));
    }
    
    
    /** pour afficher les roles
     * Get getRolesDisplay
     *
     * @return string
     */
    public function getRolesDisplay()
    {
        $rolesArray = $this->getRoles();
               
        $liste="";
        foreach ($rolesArray as $role) 
        {
            $liste .= " " . User::convertRoleToLabel($role);
        }
        
        return $liste;
    }
}