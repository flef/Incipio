<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\MissionsType;
use mgate\SuiviBundle\Entity\Mission;


class MissionsController extends Controller
{
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        $originalMissions = array();
        // Create an array of the current Mission objects in the database
        foreach ($etude->getMissions() as $mission) {
            $originalMissions[] = $mission;
        }
        

        $form = $this->createForm(new MissionsType, $etude);
        
        if( $this->get('request')->getMethod() == 'POST' )
        {
            $form->bindRequest($this->get('request'));
               
            if( $form->isValid() )
            {

                if($this->get('request')->get('add'))
                {
                    $missionNew = new Mission;
                    $missionNew->setEtude($etude);
                    $etude->addMission($missionNew);
                }


                // filter $originalPhases to contain phases no longer present
                foreach ($etude->getMissions() as $mission) {
                    foreach ($originalMissions as $key => $toDel) {
                        if ($toDel->getId() === $mission->getId()) {
                            unset($originalMissions[$key]);
                        }
                    }
                    
                    if(!$mission->isKnownIntervenant() && $mission->getNewIntervenant()!=null)
                    {
                        $mission->setIntervenant($mission->getNewIntervenant());
                    }
                }
                
                // remove the relationship between the mission and the etude
                foreach ($originalMissions as $mission) {
                    foreach ($mission->getPhaseMission() as $PhaseMission) {
                        $em->remove($PhaseMission); // suppression répartition JEH
                    }
                    $em->remove($mission); // on peut faire un persist sinon, cf doc collection form
                }

                
                $em->persist( $etude ); // persist $etude / $form->getData()
                $em->flush();
                
                //Necessaire pour refraichir l ordre
                //$em->refresh($etude);
                //$form2 = $this->createForm(new MissionsType, $etude);
                // ci dessus ca bug, pourtant c'est similaire a PhasesController. Autre méthode :
                return $this->redirect( $this->generateUrl('mgateSuivi_missions_modifier', array('id' => $etude->getId())) );

            }
        }
        
        return $this->render('mgateSuiviBundle:Mission:missions.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
}
