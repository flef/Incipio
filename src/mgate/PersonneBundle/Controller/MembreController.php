<?php

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;
use mgate\PersonneBundle\Entity\Mandat;
use mgate\PersonneBundle\Form\MembreType;

class MembreController extends Controller {

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Membre')->findAll();

        return $this->render('mgatePersonneBundle:Membre:index.html.twig', array(
                    'membres' => $entities,
                ));
    }

    /**
     * @Secure(roles="ROLE_ELEVE")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgatePersonneBundle:Membre')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Membre entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgatePersonneBundle:Membre:voir.html.twig', array(
                    'membre' => $entity,
                /* 'delete_form' => $deleteForm->createView(),        */                ));
    }

    /*
     * Ajout ET modification des membres (create if membre not existe )
     */
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        if (!$membre = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($id))
            $membre = new Membre;
        if (!count($membre->getMandats()->toArray())) {
            $mandatNew = new Mandat;
            $poste = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->findOneBy(array("intitule" => "Membre"));
            $dt = new \DateTime("now");
            $dtl = clone $dt;
            $dtl->modify('+1 year');

            if ($poste)
                $mandatNew->setPoste($poste);
            $mandatNew->setMembre($membre);
            $mandatNew->setDebutMandat($dt);
            $mandatNew->setFinMandat($dtl);
            $membre->addMandat($mandatNew);
        }

        
        $form = $this->createForm(new MembreType, $membre);
        $deleteForm = $this->createDeleteForm($id);

        $mandats = $membre->getMandats()->toArray();

        $form = $this->createForm(new MembreType, $membre);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {

                if ($this->get('request')->get('add')) {
                    $mandatNew = new Mandat;
                    $poste = $em->getRepository('mgate\PersonneBundle\Entity\Poste')->findOneBy(array("intitule" => "Membre"));
                    $dt = new \DateTime("now");
                    $dtl = clone $dt;
                    $dtl->modify('+1 year');

                    if ($poste)
                        $mandatNew->setPoste($poste);
                    $mandatNew->setMembre($membre);
                    $mandatNew->setDebutMandat($dt);
                    $mandatNew->setFinMandat($dtl);
                    $membre->addMandat($mandatNew);
                }


                // remove the relationship between the phase and the etude
                foreach ($mandats as $mandat) {
                    $em->remove($mandat); // on peut faire un persist sinon, cf doc collection form
                }

                $em->persist($membre); // persist $etude / $form->getData()
                $em->flush();

                //Necessaire pour refraichir l ordre
                $em->refresh($membre);
                $form = $this->createForm(new MembreType(), $membre);
            }
        }


        return $this->render('mgatePersonneBundle:Membre:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'membre' => $membre,
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function deleteAction($id) {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            if (!$entity = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($id))
                throw $this->createNotFoundException('Membre[id=' . $id . '] inexistant');

            if ($entity->getPersonne()) {
                $entity->getPersonne()->setMembre(null);
                if ($entity->getPersonne()->getUser())// pour pouvoir réattribuer le compte
                    $entity->getPersonne()->getUser()->setPersonne(null);
                $entity->getPersonne()->setUser(null);
            }
            $entity->setPersonne(null);
            //est-ce qu'on supprime la personne aussi ?

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('mgatePersonne_membre_homepage'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
