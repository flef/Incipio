<?php
namespace mgate\PersonneBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use mgate\PersonneBundle\Entity\Personne;

class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $su = new Personne();
        $su->setNom('admin');
        $su->setPrenom('test');
        $su->setAdresse('bbbb');
        $su->setEmail('wtf@hh.fr');
        $su->setSexe('m');
      
        

        $manager->persist($su);
        $manager->flush();
    }
}
