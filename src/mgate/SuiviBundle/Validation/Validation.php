<?php

namespace mgate\SuiviBundle\Validation;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Entity\Etude as Etude;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class Validation extends ConstraintValidator
{
    protected $em;
    protected $etude;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    //vérifie 80€ < prix JEH < 300€, contrainte à mettre dans l'entity ?
    public function prixJEH(Etude $etude)
    {
        foreach($etude->getPhases() as $phase)
        {
            if(!($phase->getPrixJEH()<=300 && 80<=$phase->getPrixJEH())){ return 0;}//algo moisi car dit pas la phase qui foire
        }
        return 1;//pas de ret avant donc ok
    }
    
    public function ValidationCc(Etude $etude)
    {
        $dateSignatureAp = $etude->getAp()->getDateSignature();
        $dateSignatureCc = $etude->getCc()->getDateSignature();
        
        if($dateSignatureAp<=$dateSignatureCc)
        {
            return 1;
        }
        else return 0;
        
    }
    
    //check date de signature rm par rapport à cc
    public function RmDate(Etude $etude)
    {
        $dateSignatureRm = $etude->getOm()->getDateSignature();
        $dateSignatureCc = $etude->getCc()->getDateSignature();
        
        if($dateSignatureCc<=$dateSignatureRm)
        {
            return 1;
        }
        else return 0;
    }
    
    //check date début et fin du rm par rapport aux phases
    public function RmDatePhase(Etude $etude)
    {
        $dateDebutRm = $etude->getOm()->getDebutOm();
        $dateFinRm = $etude->getOm()->getFinOm();
        
        $dateDebutEtude = $etude->getCc()->getDateSignature();
        $dateFinEtude = $this->get('mgate.etude_manager')->getDateFin($etude);
        
        if($dateDebutEtude <= $dateDebutRm && $dateDebutRm <= $dateFinRm && $dateFinRm <= $dateFinEtude)
        {
            return 1;
        }
        else return 0;
    }
    
    //check que tout les JEH sont reversés aux étudiants
    public function ValidationJEH(Etude $etude)
    {
      
        $jehEtudiant = 0;
        $jehClient = 0;
        foreach($etude->getPhases() as $phase)
        {
            foreach($phase->getPhaseMission() as $phaseMission)
            {
                $jehEtudiant = $jehEtudiant + $phaseMission->getNbrJEH();
            }
            
            $jehClient = $jehClient + $phase->getNbrJEH();
        }
        
        if($jehClient == $jehEtudiant)
        {
            return 1;//ok tout les jeh sont reversés aux étudiants
        }
        else return 0;
    }
    
    public function PviDate(Etude $etude)
    {
        
    }
    
   
    

    
}