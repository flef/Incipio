<?php

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Manager\BaseManager;
use mgate\SuiviBundle\Entity\Etude as Etude;

class EtudeManager extends BaseManager
{
    protected $em;
    protected  $tva;

    public function __construct(EntityManager $em, $tva)
    {
        $this->em = $em;
        $this->tva = $tva;
    }

    /**
     * Get montant total HT
     */
    public function getTotalJEHHT(Etude $etude)
    {
        $total=0;
        foreach ($etude->getPhases() as $phase) {
            $total += $phase->getNbrJEH()*$phase->getPrixJEH();
        }
        
        return $total;
    }
    /**
     * Get montant total HT
     */
    public function getTotalHT(Etude $etude)
    {
        $total = $etude->getFraisDossier()+getTotalJEHHT($etude);
        
        return $total;
    }
    
    /**
     * Get montant total HT
     */
    public function getNbrJEH(Etude $etude)
    {
        $total = 0;
        
        foreach ($etude->getPhases() as $phase) {
            $total += $phase->getPrixJEH();
        }
        
        return $total;
    }
    
    /**
     * Get montant total TTC
     */
    public function getTotalTTC(Etude $etude)
    {      
        return $this->getTotalHT($etude)*(1+$this->tva);
    }
    

    public function getRepository()
    {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }

}