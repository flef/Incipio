<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Form\MissionsType;
use mgate\SuiviBundle\Entity\Mission;

class MissionsController extends Controller {

        
    /**
     * @todo A supprimer
     * @abstract Fonction qui assure la transition entre l'ancienne et la nouvelle répartition JEH
     */
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function majBDDAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();
        

        foreach ($entities as $etude){
            foreach ($etude->getMissions() as $mission){
                $NbrJEH = 0;
                $Total = 0;
                foreach ($mission->getPhaseMission() as $phaseMission){
                    $NbrJEH += $phaseMission->getNbrJEH();
                    $Total += $phaseMission->getNbrJEH() * $phaseMission->getPhase()->getPrixJEH();
                }
                 if(count($mission->getRepartitionsJEH()->toArray()) !== 0)
                    throw $this->createNotFoundException ('CheckDatabase Manually on etude '.$etude->getId());
                if($NbrJEH){
                    $repartition = new \mgate\SuiviBundle\Entity\RepartitionJEH;
                    $repartition->setNbrJEH($NbrJEH);
                    $repartition->setPrixJEH($Total/$NbrJEH);
                    $repartition->setMission($mission);
                    $mission->addRepartitionsJEH($repartition);
                }               
            }
            
        $em->persist($etude);
        $em->flush();
        }

        return $this->render('mgateSuiviBundle:Etude:majBDD.html.twig', array(
                    'etudes' => $entities,
                ));
    }
        
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Etude:index.html.twig', array(
                    'etudes' => $entities,
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id))
            throw $this->createNotFoundException('Etude[id=' . $id . '] inexistant');

        $missionsToRemove = $etude->getMissions()->toArray();

        $repartitionsToRemove = array();
        foreach ($missionsToRemove as $mission)
            array_push($repartitionsToRemove, $mission->getRepartitionsJEH()->toArray());

        $form = $this->createForm(new MissionsType, $etude);
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {

                if ($this->get('request')->get('add')) {
                    $missionNew = new Mission;
                    $missionNew->setEtude($etude);
                    $etude->addMission($missionNew);
                }

                if ($this->get('request')->get('addRepartition')) {
                    $repartitionNew = new \mgate\SuiviBundle\Entity\RepartitionJEH;

                    if ($this->get('request')->get('idMission') !== NULL) {
                        $idMission = intval($this->get('request')->get('idMission'));
                        if ($etude->getMissions()->get($idMission)) {
                            $mission = $etude->getMissions()->get($this->get('request')->get('idMission'));
                            $mission->addRepartitionsJEH($repartitionNew);
                            $repartitionNew->setMission($mission);
                            
                            $repartitionNew->setNbrJEH(0);
                            $repartitionNew->setPrixJEH(300);
                        }
                    }
                }

                // Recherche des missions à supprimer de la BDD
                foreach ($etude->getMissions() as $mission) {
                    if (!$mission->isKnownIntervenant() && $mission->getNewIntervenant() != null)
                        $mission->setIntervenant($mission->getNewIntervenant());

                    $key = array_search($mission, $missionsToRemove);
                    if ($key !== FALSE) { // L'entité est trouvée, elle ne doit pas être supprimée
                        unset($missionsToRemove[$key]);

                        // Recherche des répartitions à supprimer de la BDD
                        foreach ($mission->getRepartitionsJEH() as $repartition) {
                            $keyJEH = array_search($repartition, $repartitionsToRemove[$key]);
                            if ($keyJEH !== FALSE) // L'entité est trouvée, elle ne doit pas être supprimée
                                unset($repartitionsToRemove[$key][$keyJEH]);
                        }
                    }
                }
                //Suppression des missions à supprimer de la BDD
                foreach ($missionsToRemove as $mission)
                    $em->remove($mission);
                // Suppression de la BDD des répartitions à supprimer de la BDD
                foreach ($repartitionsToRemove as $repartitions){
                    foreach ($repartitions as $repartition)
                    $em->remove($repartition);
                }

                $em->persist($etude);
                $em->flush();

                return $this->redirect($this->generateUrl('mgateSuivi_missions_modifier', array('id' => $etude->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:Mission:missions.html.twig', array(
                    'form' => $form->createView(),
                    'etude' => $etude,
                ));
    }

}

