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
use mgate\TresoBundle\Entity\CotisationURSSAF;

class LoadCotisationURSSAFData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {   
        $cotisations = array();
        
        /*
         * BV TYPE 2014
         */
        $cotisations[] = array(
            'libelle' => 'C.R.D.S. + CSG non déductible',
            'isBaseUrssaf' => true,
            'tauxJE' => 0,
            'tauxEtu' => 0.029,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'C.S.G.',
            'isBaseUrssaf' => true,
            'tauxJE' => 0,
            'tauxEtu' => 0.051,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance maladie',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.1280,
            'tauxEtu' => 0.0075,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Contribution solidarité autonomie',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.0030,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance vieillesse déplafonnée',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.0175,
            'tauxEtu' => 0.0025,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance vieillesse plafonnée TA',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.0845,
            'tauxEtu' => 0.0680,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Accident du travail',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.0150,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Allocations familliales',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.0525,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Fond National d\'Aide au Logement',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.0010,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Versement Transport',
            'isBaseUrssaf' => true,
            'tauxJE' => 0,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance chômage',
            'isBaseUrssaf' => false,
            'tauxJE' => 0.0400,
            'tauxEtu' => 0.0240,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'AGS',
            'isBaseUrssaf' => false,
            'tauxJE' => 0.030,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );        
        
        
        foreach ($cotisations as $cotisation){
            $cotisationURSSAF = new CotisationURSSAF();
            
            $cotisationURSSAF
                ->setDateDebut($cotisation['dateDebut'])
                ->setDateFin($cotisation['dateFin'])
                ->setIsSurBaseURSSAF($cotisation['isBaseUrssaf'])
                ->setLibelle($cotisation['libelle'])
                ->setTauxPartEtu($cotisation['tauxEtu'])
                ->setTauxPartJE($cotisation['tauxJE']);
            
            if(!$manager->getRepository('mgateTresoBundle:CotisationURSSAF')->findBy(array(
                'dateDebut' => $cotisationURSSAF->getDateDebut(),
                'dateFin'   => $cotisationURSSAF->getDateFin(),
                'libelle'   => $cotisationURSSAF->getLibelle(),
            )))
            $manager->persist($cotisationURSSAF);
        }
        $manager->flush();
        
    }
}