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
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Membre')->findAll();

        return $this->render('mgatePersonneBundle:Membre:index.html.twig', array(
                    'membres' => $entities,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function listIntervenantsAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();
        
        $intervenants = array();
        
        
        foreach($entities as $etude){
            foreach($etude->getMissions() as $mission){
                $intervenant = $mission->getIntervenant();
                if($intervenant != NULL)
                    $intervenants[$intervenant->getPersonne()->getPrenomNom()] = $intervenant;          
            }
        }

        return $this->render('mgatePersonneBundle:Membre:indexIntervenants.html.twig', array(
                    'membres' => $intervenants,
                ));
    }
    
    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function statistiqueAction($page) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Membre')->findAll();
        
            
        $membresActifs = array();
        foreach($entities as $membre){
            foreach ($membre->getMandats() as $mandat){
                if($mandat->getPoste()->getIntitule() == 'Membre' && $mandat->getDebutMandat() < new \DateTime("now") && $mandat->getFinMandat() > new \DateTime("now"))
                    $membresActifs[] = $membre;
            }                
        }
        return $this->render('mgatePersonneBundle:Membre:index.html.twig', array(
                    'membres' => $membresActifs,
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
        
        $path =  $entity->getPromotion() .'/' . 
       		preg_replace(
        		'#[^a-zA-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜáàâäéèêëíìîïóòôöúùûüÇç\-_]#',
        		'_',
			mb_strtolower($entity->getPersonne()->getNom(),'UTF-8')
		) . '_'.
        	preg_replace(
        		'#[^a-zA-Z0-9ÁÀÂÄÉÈÊËÍÌÎÏÓÒÔÖÚÙÛÜáàâäéèêëíìîïóòôöúùûüÇç\-_]#',
        		'_',
			mb_strtolower($entity->getPersonne()->getPrenom(), 'UTF-8')
		) . '.jpg';
        $promo = $entity->getPromotion();
        
	
        if(file_exists('images/photos/P'. $path))
            $pictureURL = 'images/photos/P'. $path;
        else{
            $pictureURL = 'http://ismin.emse.fr/ismin/Photos/P' . $path;
            if (!file_exists('images/photos') and !is_dir('images/photos'))
                mkdir('images/photos');   
            if (!file_exists('images/photos/P'.$promo) and !is_dir('images/photos/P'.$promo))
                mkdir('images/photos/P'.$promo); 

            copy('http://ismin.emse.fr/ismin/Photos/P'.urlencode($path), 'images/photos/P'. $path);
        }


        return $this->render('mgatePersonneBundle:Membre:voir.html.twig', array(
                    'membre' => $entity,
                    'pictureURL' => $pictureURL,
        ));
    }

    /*
     * Ajout ET modification des membres (create if membre not existe )
     */

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id) {
        $em = $this->getDoctrine()->getManager();

        if (!$membre = $em->getRepository('mgate\PersonneBundle\Entity\Membre')->find($id)) {
            $membre = new Membre;
            
            $now = new \DateTime("now");
            $now->modify('+ 3 year');            
            
            $membre->setPromotion($now->format("Y"));
            
            $now = new \DateTime("now");
            $now->modify('- 20 year');
            $membre->setDateDeNaissance($now);
        }
        
        if(!$membre->getEmailEMSE())
            $membre->setEmailEMSE($membre->getEmailEtu());
        /*
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
        }*/


        $form = $this->createForm(new MembreType, $membre);
        $deleteForm = $this->createDeleteForm($id);

        $mandatsToRemove = $membre->getMandats()->toArray();

        $form = $this->createForm(new MembreType, $membre);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

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

                if (!$membre->getIdentifiant()) {
                    $initial = substr($membre->getPersonne()->getPrenom(), 0, 1) . substr($membre->getPersonne()->getNom(), 0, 1);
                    $ident = count($em->getRepository('mgate\PersonneBundle\Entity\Membre')->findBy(array("identifiant" => $initial))) + 1;
                    while ($em->getRepository('mgate\PersonneBundle\Entity\Membre')->findOneBy(array("identifiant" => $initial . $ident)))
                        $ident++;
                    $membre->setIdentifiant(strtoupper($initial . $ident));
                }
                
                
                // Suppression des mandat à supprimer
                    //Recherche des mandats supprimés
                foreach ($membre->getMandats() as $mandat){
                    $key = array_search($mandat, $mandatsToRemove);
                    if($key !== FALSE)
                        array_splice($mandatsToRemove, $key, 1);
                }
                    //Supression de la BDD
                foreach ($mandatsToRemove as $mandat){
                    $em->remove($mandat);
                }
                

                $em->persist($membre); // persist $etude / $form->getData()
                $em->flush();
                
                $form = $this->createForm(new MembreType(), $membre);
            }
        }
        // TODO A modifier, l'ajout de poste dois se faire en js cf formation membre
        if ($this->get('request')->get('save'))
            return $this->redirect($this->generateUrl('mgatePersonne_membre_voir', array('id' => $membre->getId())));
        
        return $this->render('mgatePersonneBundle:Membre:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
             
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function deleteAction($id) {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

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
