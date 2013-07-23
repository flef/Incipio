<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Entity\PhaseMission;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\MissionsRepartitionType;
use mgate\SuiviBundle\Form\MissionsType;
use mgate\SuiviBundle\Entity\Mission;


class MissionsRepartitionController extends Controller
{
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($id)
    {
        
    }
    
}
