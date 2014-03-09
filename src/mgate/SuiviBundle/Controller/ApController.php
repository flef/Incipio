<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Entity\Ap;
use mgate\SuiviBundle\Entity\Etude;
use mgate\SuiviBundle\Entity\Prospect;
use mgate\SuiviBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Employe;
use mgate\SuiviBundle\Form\ApType;
use mgate\SuiviBundle\Form\DocTypeSuiviType;

class ApController extends Controller {
    
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
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();


        //attention reflechir si faut passer id etude ou rester en id Ap
        // en fait y a 2 fonction voir
        // une pour voir le suivi
        // et une pour voir la redaction
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id); 
        $entity = $etude->getAp();
        if (!$entity) {
            throw $this->createNotFoundException('L\'Avant-Projet demandé n\'existe pas !');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Ap:voir.html.twig', array(
                    'ap' => $entity,
                /* 'delete_form' => $deleteForm->createView(),  */                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('L\'étude demandée n\'existe pas!');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        if (!$ap = $etude->getAp()) {
            $ap = new Ap;
            $etude->setAp($ap);
        }

        $form = $this->createForm(new ApType, $etude, array('prospect' => $etude->getProspect()));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $this->get('mgate.doctype_manager')->checkSaveNewEmploye($etude->getAp());

                $em->flush();
                
                if($this->get('request')->get('phases'))
                    return $this->redirect($this->generateUrl('mgateSuivi_phases_modifier', array('id' => $etude->getId())));
                else
                    return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $etude->getNumero())));
            }
        }

        return $this->render('mgateSuiviBundle:Ap:rediger.html.twig', array(
                    'form' => $form->createView(),
                    'etude' => $etude,
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function SuiviAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('L\'étude demandée n\'existe pas!');
        }
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');
			
        $ap = $etude->getAp();
        $form = $this->createForm(new DocTypeSuiviType, $ap); //transmettre etude pour ajouter champ de etude

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $em->flush();
                return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('numero' => $etude->getNumero())));
            }
        }

        return $this->render('mgateSuiviBundle:Ap:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'etude' => $etude,
                ));
    }

}
