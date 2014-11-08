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
use mgate\SuiviBundle\Entity\Mission;
use mgate\SuiviBundle\Form\MissionType;

class MissionController extends Controller {

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
    public function avancementAction() {
         $em = $this->getDoctrine()->getManager();

       
        $avancement = isset($_POST['avancement']) ? intval($_POST['avancement']) : 0;
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $intervenant = isset($_POST['intervenant']) ? intval($_POST['intervenant']) : 0;


        $etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id);
            if (!$etude) {
                throw $this->createNotFoundException('L\'étude n\'existe pas !');
            } else {
                $etude->getMissions()->get($intervenant)->setAvancement($avancement);
                $em->persist($etude->getMissions()->get($intervenant));
                $em->flush();
            }
        
        return new Response('ok !');
         
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$mission = $em->getRepository('mgate\SuiviBundle\Entity\Mission')->find($id)) {
            throw $this->createNotFoundException('La mission n\'existe pas !');
        }
		
		$etude = $mission->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette �tude est confidentielle');

        $form = $this->createForm(new MissionType, $mission);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->flush();
                return $this->redirect($this->generateUrl('mgateSuivi_mission_voir', array('id' => $mission->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:Mission:rediger.html.twig', array(
                    'form' => $form->createView(),
                    'mission' => $mission,
                ));
    }

}
