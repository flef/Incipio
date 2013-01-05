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
        $total = $etude->getFraisDossier()+$this->getTotalJEHHT($etude);
        
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
    
    
    /**
     * Get nouveau numéro d'etude, pour valeur par defaut dans formulaire
     */
    public function getNouveauNumero($mandat=5)
    {      
        $qb = $this->em->createQueryBuilder();
        
        $query = $qb->select('e.num')
                   ->from('mgateSuiviBundle:Etude', 'e')
                   ->andWhere('e.mandat = :mandat')
                       ->setParameter('mandat', $mandat)
                   ->orderBy('e.num', 'DESC');
                    
        $value=$query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if($value)
            return $value['num']+1;
        else
            return 1;
    }
    

    public function getRepository()
    {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }

}