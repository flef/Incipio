<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\MissionsType;
use mgate\SuiviBundle\Entity\Mission;


class MissionsController extends Controller
{
    
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
            'etudes' => $entities,
        ));
         
    }

    public function modifierAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        if( ! $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id) )
        {
            throw $this->createNotFoundException('Etude[id='.$id.'] inexistant');
        }
        
        $originalMissions = array();
        // Create an array of the current Mission objects in the database
        foreach ($etude->getPhases() as $mission) {
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
                    //$missionNew->setPosition(count($etude->getMissions()));
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
                }
                
                // remove the relationship between the mission and the etude
                foreach ($originalMissions as $mission) {
                    $em->remove($mission); // on peut faire un persist sinon, cf doc collection form
                }

                
                $em->persist( $etude ); // persist $etude / $form->getData()
                $em->flush();
                
                //Necessaire pour refraichir l ordre
                $em->refresh($etude);
                $form = $this->createForm(new MissionsType, $etude);
                //return $this->redirect( $this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())) );

            }
        }
        
        return $this->render('mgateSuiviBundle:Mission:missions.html.twig', array(
            'form' => $form->createView(),
            'etude' => $etude,
        ));
    }
    
}
