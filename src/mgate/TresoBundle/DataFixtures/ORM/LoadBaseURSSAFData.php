<?php

namespace mgate\TresoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use mgate\TresoBundle\Entity\BaseURSSAF;

class LoadBaseURSSAFData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {   
        $base = array(
            2014 => 38.12,
            2013 => 37.72,
            2012 => 36.88,
            2011 => 36,
            2010 => 35.44,
            2009 => 34.84,
            2008 => 33.76,
            2007 => 33.08,
        );
        for($y = 2009; $y < 2015; $y++){
            $baseURSSAF = new BaseURSSAF;
            if(key_exists($y, $base)){
                $baseURSSAF->setBaseURSSAF($base[$y])->setDateDebut (new \DateTime("$y-01-01"))->setDateFin (new \DateTime("$y-12-31"));
                $manager->persist($baseURSSAF);
            }
        }
        if(!$manager->getRepository('mgateTresoBundle:BaseURSSAF')->findBy(array(
            'dateDebut' => $baseURSSAF->getDateDebut(),
            )))
            $manager->flush();
    }
}