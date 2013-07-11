<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Mandat;
use mgate\PersonneBundle\Form\MembreType;

class PersonneController extends Controller {
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function annuaireAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->findAll();

        return $this->render('mgatePersonneBundle:Personne:annuaire.html.twig', array(
                    'personnes' => $entities,
                ));
    }
}