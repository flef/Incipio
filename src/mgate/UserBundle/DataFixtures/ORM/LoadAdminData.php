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
       
        
        $personne = new Personne();
        $personne->setNom('Super');
        $personne->setPrenom('Admin');
        $personne->setAdresse('879 route de Mimet - 13120');
        $personne->setEmail($this->container->getParameter('su_mail'));
        $personne->setSexe('m');
        
        $membre = new Membre();
        $membre->setPersonne($personne);
        
        $su = new User();
        $su->setUsername($this->container->getParameter('su_username')); //mettre le login de l'admin
        $su->setPlainPassword($this->container->getParameter('su_password')); //mettre le mdp de l'admin
        $su->setEmail($this->container->getParameter('su_mail'));
        $su->setPersonne($personne);
        
        $personne->setUser($su);
        
        $manager->persist($membre);
        $manager->persist($personne);
        $manager->persist($su);
        $manager->flush();
    }
}
