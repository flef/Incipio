<?php

namespace mgate\SuiviBundle\Validation;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Entity\Etude as Etude;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateValidation extends ConstraintValidator
{
    protected $em;
    protected $etude;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function CcDate(Etude $etude)
    {
        $dateSignatureAp = $etude->getAp()->getDateSignature();
        $dateSignatureCc = $etude->getCc()->getDateSignature();
        
        if($dateSignatureAp<=$dateSignatureCc)
        {
            return 1;
        }
        else return 0;
        
    }
    
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
    
    public function PviDate(Etude $etude)
    {
        
    }
    

    
}