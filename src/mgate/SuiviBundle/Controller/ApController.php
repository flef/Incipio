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
use mgate\SuiviBundle\Form\ApHandler;
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
        $etude = $em->getRepository('mgateSuiviBundle:Etude')->find($id); // Ligne qui posse problème
        $entity = $etude->getAp();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ap entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return $this->render('mgateSuiviBundle:Ap:voir.html.twig', array(
                    'ap' => $entity,
                /* 'delete_form' => $deleteForm->createView(),  */                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function redigerAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('Etude[id=' . $id . '] inexistant');
        }

        if (!$ap = $etude->getAp()) {
            $ap = new Ap;
            $etude->setAp($ap);
        }

        $form = $this->createForm(new ApType, $etude, array('prospect' => $etude->getProspect()));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $this->get('mgate.doctype_manager')->checkSaveNewEmploye($etude->getAp());

                $em->flush();
                
                if($this->get('request')->get('phases'))
                    return $this->redirect($this->generateUrl('mgateSuivi_phases_modifier', array('id' => $etude->getId())));
                else
                    return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())));
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
    public function genererAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('Etude[id=' . $id . '] inexistant');
        }



        $version = $etude->getAp()->getVersion();
        $dateSignature = $etude->getAp()->getDateSignature();
        $fraisDossier = $etude->getFraisDossier();
        $presentationProjet = $etude->getPresentationProjet();
        $descriptionPrestation = $etude->getDescriptionPrestation();
        $typePrestation = $etude->getTypePrestation();
        $competences = $etude->getCompetences();
        $phases = $etude->getPhases(); // tester avec foreach
        $prospect = $etude->getProspect(); // tester avec foreach
        $suiveur = $etude->getSuiveur(); // tester avec boucle foreach
        $signataire1 = $etude->getAp()->getSignataire1(); //suiveur
        $signataire2 = $etude->getAp()->getSignataire2(); // tester avec foreach
        $test = array(
            'Version' => $version,
            'Frais de dossier' => $fraisDossier,
            'Presentation du projet' => $presentationProjet,
            'Description de la prestation' => $descriptionPrestation,
            'Type de prestation' => $typePrestation,
            'Competences' => $competences,
            'Date de signature' => $dateSignature);

        if ($signataire2 != NULL)
            $testSignataire2 = array('Prenom du signataire client' => $signataire2->getPrenom(),
                'Poste du signataire client' => $signataire2->getPoste(),
                'Nom du signataire client' => $signataire2->getNom()
            );
        if ($suiveur != NULL)
            $testSuiveur = array('Nom du suiveur' => $suiveur->getNom(),
                'Prenom du suiveur' => $suiveur->getPrenom(),
                'Mobile du suiveur' => $suiveur->getMobile(),
                'Mail du suiveur' => $suiveur->getEmail()
            );
        if ($prospect != NULL)
            $testProspect = array('Nom du prospect' => $prospect->getNom(),
                'Entite' => $prospect->getEntite(),
                'Adresse du prospect' => $prospect->getAdresse()
            );
        $etude->getAp()->setGenerer(1); //initialisation avant test

        foreach ($phases as $cle => $phase) {
            $testPhase = array('Nombre de JEH de la phase' => $phase->getNbrJEH(),
                'Prix du JEH de la phase' => $phase->getPrixJEH(),
                'Titre de la phase' => $phase->getTitre(),
                'Objectif de la phase' => $phase->getObjectif(),
                'Méthodologie de la phase' => $phase->getMethodo(),
                'Début de la phase' => $phase->getDatedebut(),
                'Délai de la phase' => $phase->getDelai()
            );

            foreach ($testPhase as $cle => $element) {
                if (empty($element)) {
                    $etude->getAp()->setGenerer(0);
                    $manquant[] = $cle;
                }
            }
        }

        foreach ($test as $cle => $element) {
            if (empty($element)) {
                $etude->getAp()->setGenerer(0);
                $manquant[] = $cle;
            }
        }

        if ($suiveur != NULL)
            foreach ($testSuiveur as $cle => $element) {
                if (empty($element)) {
                    $etude->getAp()->setGenerer(0);
                    $manquant[] = $cle;
                }
            } else {
            $manquant[] = 'Information sur le suiveur';
            $etude->getAp()->setGenerer(0);
        }

        if ($prospect != NULL)
            foreach ($testProspect as $cle => $element) {
                if (empty($element)) {
                    $etude->getAp()->setGenerer(0);
                    $manquant[] = $cle;
                }
            } else {
            $manquant[] = 'Information sur le prospect';
            $etude->getAp()->setGenerer(0);
        }

        if ($signataire2 != NULL)
            foreach ($testSignataire2 as $cle => $element) {
                if (empty($element)) {
                    $etude->getAp()->setGenerer(0);
                    $manquant[] = $cle;
                }
            } else {
            $manquant[] = 'Information sur le signataire2';
            $etude->getAp()->setGenerer(0);
        }

        $manquant[] = "0"; // nécessaire pour l'initialiser si generer=1    
        $generer = $etude->getAp()->getGenerer(); // ne pas bouger car on doit récupérer la valeur de générer après vérification
        $validation = $this->get('mgate.validation')->prixJEH($etude);
        

        return $this->render('mgateSuiviBundle:Ap:generer.html.twig', array(
                    'suiveur' => $suiveur,
                    'prospect' => $prospect,
                    'version' => $version,
                    'dateSignature' => $dateSignature,
                    'fraisDossier' => $fraisDossier,
                    'presentationProjet' => $presentationProjet,
                    'descriptionPrestation' => $descriptionPrestation,
                    'typePrestation' => $typePrestation,
                    'competences' => $competences,
                    'phases' => $phases,
                    'generer' => $generer,
                    'signataire2' => $signataire2,
                    'manquants' => $manquant,
                    'etude' => $etude,
                    'validationJEH' => $validation// pour moi faut transmettre que ça, m'enfin && Je suis d'accord avec toi sur ce coup...
                ));
      }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function SuiviAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('Etude[id=' . $id . '] inexistant');
        }
        $ap = $etude->getAp();
        $form = $this->createForm(new DocTypeSuiviType, $ap); //transmettre etude pour ajouter champ de etude

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $em->flush();
                return $this->redirect($this->generateUrl('mgateSuivi_etude_voir', array('id' => $etude->getId())));
            }
        }

        return $this->render('mgateSuiviBundle:Ap:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'etude' => $etude,
                ));
    }

}
