<?php

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Manager\BaseManager;
use mgate\SuiviBundle\Entity\Etude as Etude;

class EtudeManager extends \Twig_Extension {

    protected $em;
    protected $tva;

    public function __construct(EntityManager $em, $tva) {
        $this->em = $em;
        $this->tva = $tva;
    }

    // Pour utiliser les fonctions depuis twig
    public function getName() {
        return 'mgate_EtudeManager';
    }

    // Pour utiliser les fonctions depuis twig
    public function getFunctions() {
        return array(
            'getRefEtude' => new \Twig_Function_Method($this, 'getRefEtude'),
            'getTotalHT' => new \Twig_Function_Method($this, 'getTotalHT'),
            'getNbrJEH' => new \Twig_Function_Method($this, 'getNbrJEH'),
            'getDateLancement' => new \Twig_Function_Method($this, 'getDateLancement'),
            'getDateFin' => new \Twig_Function_Method($this, 'getDateFin'),
        );
    }

    /**
     * Get montant total des JEH HT
     */
    public function getTotalJEHHT(Etude $etude) {
        $total = 0;
        foreach ($etude->getPhases() as $phase) {
            $total += $phase->getNbrJEH() * $phase->getPrixJEH();
        }

        return $total;
    }

    /**
     * Get montant total HT
     */
    public function getTotalHT(Etude $etude) {
        $total = $etude->getFraisDossier() + $this->getTotalJEHHT($etude);

        return $total;
    }

    /**
     * Get montant total TTC
     */
    public function getTotalTTC(Etude $etude) {
        return round($this->getTotalHT($etude) * (1 + $this->tva), 2);
    }

    /**
     * Get nombre de JEH
     */
    public function getNbrJEH(Etude $etude) {
        $total = 0;

        foreach ($etude->getPhases() as $phase) {
            $total += $phase->getNbrJEH();
        }
        return $total;
    }

    /**
     * Get nombre de JEH
     */
    public function getMontantVerse(Etude $etude) {
        $total = 0;

        foreach ($etude->getMissions() as $mission) {
            foreach ($etude->getPhases() as $phase) {
                $prix = $phase->getPrixJEH();
                //$mi = $etude->getMissions()->get(1);
                //TO DO faire le cas des prix de jeh différent
            }
            $total = 0.6 * $mission->getNbjeh() * $prix;
        }
        return round($total);
    }

    /**
     * Get référence de l'etude
     */
    public function getRefEtude(Etude $etude) {
        return "[M-GaTE]" . (string) ($etude->getMandat() * 100 + $etude->getNum());
    }

    /**
     * Get référence document
     */
    public function getRefDoc(Etude $etude, $doc, $version, $key = 0) {
        if ($doc == "RM") {
            if($etude->getMissions()->get($key)->getIntervenant()!=NULL)
            $identifiant = $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant();
            
            return $this->getRefEtude($etude) . "-" . $doc . "-" . $identifiant . "-" . $version;
        }
        if ($doc == "CE") {
            $identifiant = $etude->getMissions()->get($key)->getIntervenant()->getIdentifiant();
            return "[M-GaTE]" . $etude->getMandat() . "-CE-" . $identifiant;
        }

        return $this->getRefEtude($etude) . "-" . $doc . "-" . $version; //TODO faire les autres type de docs, genre RM
    }

    /**
     * Get nouveau numéro d'etude, pour valeur par defaut dans formulaire
     */
    public function getNouveauNumero($mandat = 5) {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.num')
                ->from('mgateSuiviBundle:Etude', 'e')
                ->andWhere('e.mandat = :mandat')
                ->setParameter('mandat', $mandat)
                ->orderBy('e.num', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value)
            return $value['num'] + 1;
        else
            return 1;
    }

    public function getDateLancement(Etude $etude) {
        $dateDebut = array();
        $phases = $etude->getPhases();
        
        foreach ($phases as $phase)
            if ($phase->getDateDebut() != NULL)
                array_push($dateDebut, $phase->getDateDebut());
            
        if (count($dateDebut) > 0)
            return min($dateDebut);
        else
            return NULL;
    }

    public function getDateFin(Etude $etude) {
        $dateFin = array();
        $phases = $etude->getPhases();

        foreach ($phases as $p) {
            if ($p->getDateDebut() != NULL) {
                $dateDebut = clone $p->getDateDebut(); //WARN $a = $b : $a pointe vers le même objet que $b...
                array_push($dateFin, $dateDebut->modify('+' . $p->getDelai() . ' day'));
                unset($dateDebut);
            }
        }
        
        if (count($dateFin) > 0)
            return max($dateFin);
        else
            return NULL;
 
    }

    public function getDelaiEtude(Etude $etude) {
        /*$phases = $etude->getPhases();
        $delai = 0;
        foreach ($phases as $phase)
        {
           $delai+=$phase->getDelai();
        }
        return $this->jourVersSemaine($delai);*/
       if($this->getDateFin($etude))
       return $this->getDateFin($etude)->diff($this->getDateLancement($etude));
    }

    public function getRepository() {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }
    
    

}