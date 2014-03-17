<?php

namespace mgate\TresoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use mgate\TresoBundle\Entity\Compte;

class LoadCompteData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $comptes = array(
            622600 => 'Honoraires BV',
            645100 => 'Cotisations à l\'Urssaf',
            705000 => 'Etudes',
            708500 => 'Ports et frais accessoires facturés',
            419100 => 'Clients - Avances et acomptes reçus sur commandes',
        );
        
        foreach ($comptes as $key => $value){
            $compte = new Compte();
            $compte->setCategorie(false)->setLibelle($value)->setNumero($key);
            $manager->persist($compte);
        }
        if(!$manager->getRepository('mgateTresoBundle:Compte')->findBy(array('numero' => $compte->getNumero())))
            $manager->flush();
    }
}