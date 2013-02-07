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
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }   
        
        //Creation des relations avec les phases et les missions.
        foreach ($etude->getPhases() as $phase) {
            foreach ($etude->getMissions() as $mission) {
                if( !$em->getRepository('mgate\SuiviBundle\Entity\PhaseMission')->findBy(array('phase' => $phase, 'mission' => $mission)) )
                {
                    echo "test";
                    $relation = new PhaseMission;
                    $relation->setMission($mission);
                    $relation->setPhase($phase);
                    $relation->setNbrJEH(0);
                    $em->persist( $relation );
                    $em->flush();
                }
            }
        }
        

        $form = $this->createForm(new MissionsRepartitionType, $etude);
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {
                                
                $em->persist( $etude ); // persist $etude / $form->getData()
                $em->flush();
                

                $form = $this->createForm(new MissionsRepartitionType, $etude);
                //return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );

            }
        }
        
        return $this->render('mgateSuiviBundle:Mission:repartition.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
}

