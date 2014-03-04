<?php
namespace mgate\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use mgate\UserBundle\Entity\User;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Membre;

class LoadAdminData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
     
        $su = $manager->getRepository('mgate\UserBundle\Entity\User')->findOneBy(array('username' => $this->container->getParameter('su_username')) );
        if( !$su )
        {
            $su = new User();
        }
        
        $su->setUsername($this->container->getParameter('su_username')); //mettre le login de l'admin
        $su->setPlainPassword($this->container->getParameter('su_password')); //mettre le mdp de l'admin
        $su->setEmail($this->container->getParameter('su_mail'));
        $su->setEnabled(true);
        $su->setRoles(array('ROLE_SUPER_ADMIN'));        
        
        
        //$manager->persist($personne);
        $manager->persist($su);
        $manager->flush();
    }
}
