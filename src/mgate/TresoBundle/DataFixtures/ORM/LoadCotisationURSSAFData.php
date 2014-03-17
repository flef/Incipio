<?php

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
            'tauxEtu' => 2.90,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'C.S.G.',
            'isBaseUrssaf' => true,
            'tauxJE' => 0,
            'tauxEtu' => 5.10,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance maladie',
            'isBaseUrssaf' => true,
            'tauxJE' => 12.80,
            'tauxEtu' => 0.75,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Contribution solidarité autonomie',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.30,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance vieillesse déplafonnée',
            'isBaseUrssaf' => true,
            'tauxJE' => 1.75,
            'tauxEtu' => 0.25,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Assurance vieillesse plafonnée TA',
            'isBaseUrssaf' => true,
            'tauxJE' => 8.45,
            'tauxEtu' => 6.80,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Accident du travail',
            'isBaseUrssaf' => true,
            'tauxJE' => 1.50,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Allocations familliales',
            'isBaseUrssaf' => true,
            'tauxJE' => 5.25,
            'tauxEtu' => 0,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'Fond National d\'Aide au Logement',
            'isBaseUrssaf' => true,
            'tauxJE' => 0.10,
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
            'tauxJE' => 4.00,
            'tauxEtu' => 2.40,
            'dateDebut' => new \DateTime('2014-01-01'),
            'dateFin' => new \DateTime('2014-12-31'),
            );
        
        $cotisations[] = array(
            'libelle' => 'AGS',
            'isBaseUrssaf' => false,
            'tauxJE' => 0.30,
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