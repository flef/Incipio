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
            'getErrors' => new \Twig_Function_Method($this, 'getErrors'),
            'getWarnings' => new \Twig_Function_Method($this, 'getWarnings'),
            'getInfos' => new \Twig_Function_Method($this, 'getInfos'),
            'getEtatDoc' => new \Twig_Function_Method($this, 'getEtatDoc'),
        );
    }
    
    /***
     * 
     * Juste un test
     */
     public function getFilters() {
        return array(
            'nbsp' => new \Twig_Filter_Method($this, 'nonBreakingSpace'),
        );
    }
 
    public function nonBreakingSpace($string) {
        return preg_replace('#\s#', '&nbsp;', $string);    
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
        if ($doc == "FA") {
            return $this->getRefEtude($etude) . "-" . $doc;
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
            if ($p->getDateDebut()!=NULL && $p->getDelai()!=NULL ) {
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
        if($this->getDateFin($etude))
            return $this->getDateFin($etude)->diff($this->getDateLancement($etude));
    }

    public function getRepository() {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }
    
    
    public  function getErrors(Etude $etude)
    {
      $errors = array();
        
      if($etude->getAp()!=NULL && $etude->getCc()!=NULL)
      {
        if($etude->getAp()->getDateSignature() > $etude->getCc()->getDateSignature())
        {
              $error = array('titre' => 'AP, CC - Date de signature : ', 'message' => 'La date de signature de l\'Avant Projet doit être antérieure
                  ou égale à la date de signature de la Convention Client.');  
              array_push($errors, $error);
        }
      }
      
      foreach($etude->getMissions() as $mission)
      {
        if($etude->getCc()->getDateSignature()>=$mission->getDateSignature())
        {
            $error = array('titre' => 'RM, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure
                  ou égale à la date de signature des récapitulatifs de mission.'); 
            array_push($errors, $error);
        }
      }
      
      foreach($etude->getPvis() as $pvi)
      {
        if($etude->getCc()->getDateSignature()>=$pvi->getDateSignature())
        {
            $error = array('titre' => 'PVIS, CC  - Date de signature : ', 'message' => 'La date de signature de la Convention Client doit être antérieure
                 à la date de signature des PVIS.'); 
            array_push($errors, $error);
        }
      }
      
      //ordre PVI
      foreach($etude->getPvis() as $pvi)
      {
          if(isset($pviAnterieur))
          {
            if($pvi->getDateSignature()<=$pviAnterieur->getDateSignature())
            {
                $error = array('titre' => 'PVIS - Date de signature : ', 'message' => 'La date de signature du PVI1 doit être antérieure à celle du PVI2 et ainsi de suite.
               '); 
                array_push($errors, $error);
            }
          }
          $pviAnterieur = $pvi;
      }
      
      foreach($etude->getMissions() as $mission)
      {
          foreach($etude->getPvis() as $pvi)
          {
              if($mission->getDateSignature()>=$pvi->getDateSignature())
              {
                  $error = array('titre' => 'PVIS, RM  - Date de signature : ', 'message' => 'La date de signature des Récapitulatifs de Missions doivent être antérieure
                 à la date de signature des PVIS.'); 
                    array_push($errors, $error);
              }
          }
      }
      
      if($etude->getPvr())
      {
        if($etude->getPvr()->getDateSignature()>=$etude->getDateFin())
        {
               $error = array('titre' => 'PVR  - Date de signature : ', 'message' => 'La date de signature du PVR doit être antérieure
                   à la date de fin de l\'étude. Consulter Convention Client ou Avenant à la Convention Client pour la fin l\'étude.'); 
                      array_push($errors, $error);
        }
      }
      
      //TODO ordre date signature des PVIS
        
        return $errors;
        
    }
    
    public  function getWarnings(Etude $etude)
    {
        $warnings = array();
        
        if($etude->getProspect()->getEntite()==NULL)
        {
            $warning = array('titre' => 'Entité sociale : ', 'message' => 'absente mais ce n\'est pas obligatoire');  
            array_push($warnings, $warning);
        }
        
        /*if($etude->getProspect()->getEmployes()->getPoste()==NULL)//foreach ?
        {
            $warning = array('titre' => 'Entité sociale : ', 'message' => 'absente mais ce n\'est pas obligatoire'); 
            array_push($warnings, $warning);
        }*/
       
        //array_push($warnings, $warning);
        
        return $warnings;
        
    }
    
    public  function getInfos(Etude $etude)
    {
        $infos = array();
        
        //$info = array('titre' => 'Mise à jours', 'message' => 'Message de test :D');    
        //array_push($infos, $info);
        
        return $infos;
        
    }
      
    public  function getEtatDoc($doc)
    {
        if($doc != null)
        {
            $ok =  $doc->getRedige()
                && $doc->getRelu()
                && $doc->getSpt1()
                && $doc->getSpt2()
                && $doc->getEnvoye()
                && $doc->getReceptionne();
        }
        else
        {
            $ok = false;
        }
        return $ok;
    }

    
    /**
     * Converti le numero de mandat en année
     */
    public function mandatToString($idMandat) {
        // Mandat 0 => 2007/2008
        
        return strval(2007 + $idMandat)."/".strval(2008 + $idMandat);
    }
    
    /**
     * Get le maximum des mandats
     */
    public function getMaxMandat() {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.mandat')
                ->from('mgateSuiviBundle:Etude', 'e')
                ->orderBy('e.mandat', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value)
            return $value['mandat'];
        else
            return 0;
    }
    
    /**
     * Get le maximum des mandats par rapport à la date de Signature de signature des CC
     */
    public function getMaxMandatCc() {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('c.dateSignature')
                ->from('mgateSuiviBundle:Cc', 'c')
                ->orderBy('c.dateSignature', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        
        if ($value)
            return $this->dateToMandat($value['dateSignature']);
        else
            return 0;
    }
    
    /**
     * Converti le numero de mandat en année
     */
    public function dateToMandat(\DateTime $date) {
        // Mandat 0 => 2007/2008
        $interval = new \DateInterval('P2M20D');
        $date2 = clone $date;
        $date2->sub($interval);
        
        return intval( $date2->format('Y') )-2007;
    }
}