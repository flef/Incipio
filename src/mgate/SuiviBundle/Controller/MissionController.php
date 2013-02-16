<?php

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
                throw $this->createNotFoundException('Etude[id=' . $id . '] inexistant');
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
        $em = $this->getDoctrine()->getEntityManager();

        if (!$mission = $em->getRepository('mgate\SuiviBundle\Entity\Mission')->find($id)) {
            throw $this->createNotFoundException('Mission[id=' . $id . '] inexistant');
        }

        $form = $this->createForm(new MissionType, $mission);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

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
