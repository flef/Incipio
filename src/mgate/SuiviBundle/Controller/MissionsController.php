<?php
        
/*
This file is part of Incipio.

Incipio is an enterprise resource planning for Junior Enterprise
Copyright (C) 2012-2014 Florian Lefevre.

Incipio is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Incipio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
*/


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
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id))
            throw $this->createNotFoundException('L\'étude demandée n\'existe pas!');
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $missionsToRemove = $etude->getMissions()->toArray();

        $repartitionsToRemove = array();
        foreach ($missionsToRemove as $mission)
            array_push($repartitionsToRemove, $mission->getRepartitionsJEH()->toArray());

        $form = $this->createForm(new MissionsType, $etude);
        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

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

