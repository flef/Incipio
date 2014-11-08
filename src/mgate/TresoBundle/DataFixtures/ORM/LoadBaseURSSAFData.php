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