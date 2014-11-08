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