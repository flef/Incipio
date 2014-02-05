<?php

namespace mgate\SuiviBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\SuiviBundle\Form\EtudeType;
use mgate\SuiviBundle\Entity\Av;
use mgate\SuiviBundle\Form\AvHandler;
use mgate\SuiviBundle\Form\AvType;

class PhaseChange {

    private $position = false;
    private $nbrJEH = false;
    private $prixJEH = false;
    private $titre = false;
    private $objectif = false;
    private $methodo = false;
    private $dateDebut = false;
    private $validation = false;
    private $delai = false;
    private $etatSurAvenant = false;


    private $oldPosition;
    private $oldNbrJEH;
    private $oldPrixJEH;
    private $oldTitre;
    private $oldObjectif;
    private $oldMethodo;
    private $oldDateDebut;
    private $oldValidation;
    private $oldDelai;
    private $oldEtatSurAvenant;

    public function getEtatSurAvenant(){
        return $this->etatSurAvenant;
    }
    
    public function setEtatSurAvenant($x){
        $this->etatSurAvenant = $x;
        return $this;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($x) {
        $this->position = $x;
        return $this;
    }

    public function getNbrJEH() {
        return $this->nbrJEH;
    }

    public function getPrixJEH() {
        return $this->prixJEH;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getObjectif() {
        return $this->objectif;
    }

    public function getMethodo() {
        return $this->methodo;
    }

    public function getDateDebut() {
        return $this->dateDebut;
    }

    public function getValidation() {
        return $this->validation;
    }

    public function getDelai() {
        return $this->delai;
    }

    public function setNbrJEH($x) {
        $this->nbrJEH = $x;
        return $this;
    }

    public function setPrixJEH($x) {
        $this->prixJEH = $x;
        return $this;
    }

    public function setTitre($x) {
        $this->titre = $x;
        return $this;
    }

    public function setObjectif($x) {
        $this->objectif = $x;
        return $this;
    }

    public function setMethodo($x) {
        $this->methodo = $x;
        return $this;
    }

    public function setDateDebut($x) {
        $this->dateDebut = $x;
        return $this;
    }

    public function setValidation($x) {
        $this->validation = $x;
        return $this;
    }

    public function setDelai($x) {
        $this->delai = $x;
        return $this;
    }
    
    public function getOldPosition(){
		return $this->oldPosition;
	}

	public function setOldPosition($oldPosition){
		$this->oldPosition = $oldPosition;
        return $this;
	}

	public function getOldNbrJEH(){
		return $this->oldNbrJEH;
	}

	public function setOldNbrJEH($oldNbrJEH){
		$this->oldNbrJEH = $oldNbrJEH;
        return $this;
	}

	public function getOldPrixJEH(){
		return $this->oldPrixJEH;
	}

	public function setOldPrixJEH($oldPrixJEH){
		$this->oldPrixJEH = $oldPrixJEH;
        return $this;
	}

	public function getOldTitre(){
		return $this->oldTitre;
	}

	public function setOldTitre($oldTitre){
		$this->oldTitre = $oldTitre;
        return $this;
	}

	public function getOldObjectif(){
		return $this->oldObjectif;
	}

	public function setOldObjectif($oldObjectif){
		$this->oldObjectif = $oldObjectif;
        return $this;
	}

	public function getOldMethodo(){
		return $this->oldMethodo;
	}

	public function setOldMethodo($oldMethodo){
		$this->oldMethodo = $oldMethodo;
        return $this;
	}

	public function getOldDateDebut(){
		return $this->oldDateDebut;
	}

	public function setOldDateDebut($oldDateDebut){
		$this->oldDateDebut = $oldDateDebut;
        return $this;
	}

	public function getOldValidation(){
		return $this->oldValidation;
	}

	public function setOldValidation($oldValidation){
		$this->oldValidation = $oldValidation;
        return $this;
	}

	public function getOldDelai(){
		return $this->oldDelai;
	}

	public function setOldDelai($oldDelai){
		$this->oldDelai = $oldDelai;
        return $this;
	}
    
    public function getOldEtatSurAvenant(){
        return $this->oldEtatSurAvenant;
    }

    public function setOldEtatSurAvenant($oldEtatSurAvenant){
        $this->oldEtatSurAvenant = $oldEtatSurAvenant;
        return $this;        
    }
}

class AvController extends Controller {

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function indexAction($page) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        return $this->render('mgateSuiviBundle:Av:index.html.twig', array(
                    'etudes' => $entities,
                ));
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function addAction($id) {
        return $this->modifierAction(null, $id);
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function voirAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('mgateSuiviBundle:Av')->find($id); // Ligne qui posse problème

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cc entity.');
        }
		
		$etude = $entity->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');


        return $this->render('mgateSuiviBundle:Av:voir.html.twig', array(
                    'av' => $entity,
            ));
    }

    private function getPhaseByPosition($position, $array) {
        foreach ($array as $phase) {
            if ($phase->getPosition() == $position)
                return $phase;
        }
        return NULL;
    }

    static $phaseMethodes = array('NbrJEH', 'PrixJEH', 'Titre', 'Objectif', 'Methodo', 'DateDebut', 'Delai', 'Position', 'EtatSurAvenant');

    /**
     * @abstract copie tous les champs non null de $phaseToMerge dans Phase receptor
     * phaseRecptor est la Phase original, Phase to merge contient les champs modifiés par l'AV
     * 
     * @param Phase $phaseReceptor
     * @param Phase $phaseToMerge
     * @param PhaseChange $changes
     */
    private function mergePhaseIfNotNull($phaseReceptor, $phaseToMerge, $changes) {
        foreach (AvController::$phaseMethodes as $methode) {
            $getMethode = 'get' . $methode;
            $setMethode = 'set' . $methode;
            $setOldValue = 'setOld' . $methode;
            if ($phaseToMerge->$getMethode() != NULL) {
                $changes->$setMethode(true);
                $changes->$setOldValue($phaseReceptor->$getMethode());
                $phaseReceptor->$setMethode($phaseToMerge->$getMethode());
            }
        }
    }

    private function copyPhase($source, $destination) {
        foreach (AvController::$phaseMethodes as $methode) {
            $getMethode = 'get' . $methode;
            $setMethode = 'set' . $methode;
            $destination->$setMethode($source->$getMethode());
        }
    }

    private function phaseChange($phase) {
        $isNotNull = false;
        foreach (AvController::$phaseMethodes as $methode) {
            $getMethode = 'get' . $methode;
            $isNotNull = $isNotNull || ($phase->$getMethode() != NULL && $methode != "Position");
        }
        return $isNotNull;
    }

    private function nullFielIfEqual($phaseReceptor, $phaseToCompare) {
        $isNotNull = false;
        foreach (AvController::$phaseMethodes as $methode) {
            $getMethode = 'get' . $methode;
            $setMethode = 'set' . $methode;
            if ($phaseReceptor->$getMethode() == $phaseToCompare->$getMethode() && $methode != "Position")
                $phaseReceptor->$setMethode(NULL);
            else
                $isNotNull = true;
        }
        return $isNotNull;
    }

    /**
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function modifierAction($id, $idEtude = null) {
        $em = $this->getDoctrine()->getManager();

        if ($idEtude) {
            if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($idEtude))
                throw $this->createNotFoundException('Unable to find Etude entity.');
            $av = new Av;
            $av->setEtude($etude);
            $etude->addAv($av);
        }
        else if (!$av = $em->getRepository('mgate\SuiviBundle\Entity\Av')->find($id))
            throw $this->createNotFoundException('Unable to find Av entity.');

		
		$etude = $av->getEtude();
		
		if($this->get('mgate.etude_manager')->confidentielRefus($etude, $this->container->get('security.context')))
			throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException ('Cette étude est confidentielle');

        $phasesAv = array();
        if($av->getPhases()){
            $phasesAv = $av->getPhases()->toArray();

            foreach ($av->getPhases() as $phase) {
                $av->removePhase($phase);
                $em->remove($phase);
            }
        }

        $phasesChanges = array();

        $phasesEtude = $etude->getPhases()->toArray();
        foreach ($phasesEtude as $phase) {

            $changes = new PhaseChange();
            $phaseAV = new \mgate\SuiviBundle\Entity\Phase;

            $this->copyPhase($phase, $phaseAV);

            if ($phaseOriginAV = $this->getPhaseByPosition($phaseAV->getPosition(), $phasesAv))
                $this->mergePhaseIfNotNull($phaseAV, $phaseOriginAV, $changes);

            $phaseAV->setEtude()->setAvenant($av);
            $av->addPhase($phaseAV);
            $phasesChanges[] = $changes;

        }


        $form = $this->createForm(new AvType, $av, array('prospect' => $av->getEtude()->getProspect()));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                $phasesEtude = $av->getEtude()->getPhases()->getValues();
                foreach ($av->getPhases() as $phase) {
                    $toKeep = false;
                    $av->removePhase($phase);

                    if (!$phaseEtude = $this->getPhaseByPosition($phase->getPosition(), $phasesEtude))
                        $toKeep = true;

                    if (isset($phaseEtude)) {
                        $toKeep = $this->nullFielIfEqual($phase, $phaseEtude);
                    }

                    if ($toKeep)
                        $av->addPhase($phase);

                    unset($phaseEtude);
                }

                foreach ($av->getPhases() as $phase) {
                    $phase->setEtatSurAvenant(0);
                    if ($this->phaseChange($phase)) // S'il n'y a plus de modification sur la phase
                        $em->persist($phase);
                    else
                        $av->removePhase($phase);
                }
                
                if ($idEtude) // Si on ajoute un avenant
                    $em->persist($etude);
                else // Si on modifie un avenant
                    $em->persist($av);
                $em->flush();
                return $this->redirect($this->generateUrl('mgateSuivi_av_voir', array('id' => $av->getId())));
            }
        }
        
        return $this->render('mgateSuiviBundle:Av:modifier.html.twig', array(
                    'form' => $form->createView(),
                    'av' => $av,
                    'changes' => $phasesChanges,
                ));
    }

}
