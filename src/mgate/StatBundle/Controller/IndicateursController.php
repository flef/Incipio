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


namespace mgate\StatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Ob\HighchartsBundle\Highcharts\Highchart;
use mgate\SuiviBundle\Entity\EtudeRepository;
use mgate\PersonneBundle\Entity\MembreRepository;
use mgate\PersonneBundle\Entity\MandatRepository;

// A externaliser dans les parametres
define("STATE_ID_EN_COURS_X", 2);
define("STATE_ID_TERMINEE_X", 4);

class Indicateur {

    private $titre;
    private $methode;
    private $options;

    public function getTitre() {
        return $this->titre;
    }

    public function getMethode() {
        return $this->methode;
    }
    
    public function hasOptions(){
        return $this->options;
    }

    public function setTitre($x) {
        $this->titre = $x;
        return $this;
    }

    public function setMethode($x) {
        $this->methode = $x;
        return $this;
    }    
    
    public function setOptions($x){
        $this->options = $x;
        return $this;
    }
}

class IndicateursCollection {

    private $indicateurs;
    private $autorizedMethods;

    function __construct() {
        $this->indicateurs = array();
        $this->autorizedMethods = array();
    }

    public function addCategorieIndicateurs($categorie) {
        if (!array_key_exists($categorie, $this->indicateurs))
            $this->indicateurs[$categorie] = array();
        return $this;
    }

    public function setIndicateurs(Indicateur $indicateur, $categorie) {
        $this->indicateurs[$categorie][] = $indicateur;
        $this->setAutorizedMethods($indicateur->getMethode());
        return $this;
    }

    public function getIndicateurs($categorie = NULL) {
        if ($categorie !== NULL)
            return $this->indicateurs[$categorie];
        else
            return $categorie;
    }

    public function getAutorizedMethods() {
        return $this->autorizedMethods;
    }

    public function setAutorizedMethods($method) {
        if (is_string($method))
            array_push($this->autorizedMethods, $method);
        else
            $this->autorizedMethods = $method;
        return $this;
    }

}

class IndicateursController extends Controller {

    public $indicateursCollection;

    function __construct() {
        $this->indicateursCollection = new IndicateursCollection();
        if (isset($_SESSION['autorizedMethods']))
            $this->indicateursCollection->setAutorizedMethods($_SESSION['autorizedMethods']);
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function indexAction() {
        if (isset($_SESSION['autorizedMethods']))
            unset($_SESSION['autorizedMethods']);

        // Définition des catégories     
        $this->indicateursCollection
            ->addCategorieIndicateurs('Suivi')
            ->addCategorieIndicateurs('Rfp')
            ->addCategorieIndicateurs('Com')
            ->addCategorieIndicateurs('Treso')
            ->addCategorieIndicateurs('Gestion');

        /************************************************
         * 			Indicateurs Suivi d'études			*
         * ********************************************** */
        
        // Taux d'avenant par mandat = Rate EtudeAvecAvenant/NombreEtude
        $tauxAvenant = new Indicateur();
        $tauxAvenant->setTitre('Taux d\'avenant par mandat')
            ->setMethode('getTauxDAvenantsParMandat');
        
        // Cammember étude selon domaine de compétence 
        // TODO : Selectionner un mandat default getMaxMandat
        
        $nombreEtudes = new Indicateur();
        $nombreEtudes->setTitre('Nombre d\'études par mandat')
            ->setMethode('getNombreEtudes');
        
        $retardSurEtude = new Indicateur();
        $retardSurEtude->setTitre('Nombre de jours de retard')
            ->setMethode('getRetardParMandat');
        


        /************************************************
         * 			Indicateurs Gestion Asso			*
         * ********************************************** */
        // Nombre d'intervenants par promo 
        $ressourcesHumaines = new Indicateur();
        $ressourcesHumaines->setTitre('Nombre d\'intervenants par Promo')
            ->setMethode('getIntervenantsParPromo');

        // Nombre d'e membre par promo
        $membresParPromo = new Indicateur();
        $membresParPromo->setTitre('Nombre de Membres par Promo')
            ->setMethode('getMembresParPromo');

        // Nombre de cotisant en continu
        $membres = new Indicateur();
        $membres->setTitre('Nombre de Membres')
            ->setMethode('getNombreMembres');

        /************************************************
         * 				Indicateurs RFP					*
         * ********************************************** */
        $nombreDeFormationsParMandat = new Indicateur();
        $nombreDeFormationsParMandat->setTitre('Nombre de formations théorique par mandat')
            ->setMethode('getNombreFormationsParMandat');
        
        
        $presenceAuxFormationsTimed = new Indicateur();
        $presenceAuxFormationsTimed->setTitre('Nombre de présents aux formations')
            ->setMethode('getNombreDePresentFormationsTimed');
        
        /************************************************
         * 			Indicateurs Trésorerie 			*
         * ********************************************** */      
        //Chiffre d'affaires en fonction du temps sur les Mandats
        $chiffreAffaires = new Indicateur();
        $chiffreAffaires->setTitre('Evolution du Chiffre d\'Affaires')
            ->setMethode('getCA');

        //Chiffre d'affaires par mandat
        $chiffreAffairesMandat = new Indicateur();
        $chiffreAffairesMandat->setTitre('Evolution du Chiffre d\'Affaires par Mandat')
            ->setMethode('getCAM');
        
        //Dépense HT par mandat
        $sortieNFFA = new Indicateur();
        $sortieNFFA->setTitre('Evolution des dépenses par mandats')
            ->setMethode('getSortie');
        
        //Répartition des dépenses sur le mandat
        $repartitionSortieNFFA = new Indicateur();
        $repartitionSortieNFFA->setTitre('Répartition des dépenses sur le mandat')
            ->setMethode('getRepartitionSorties');
        
        /************************************************
         * 		Indicateurs Prospection Commerciale		*
         * ********************************************** */
        // Provenance des études (tous mandats) par type de client
        // TODO : selectionner un mandat default getMaxMandat -1 = tous les mandats
        $repartitionClient = new Indicateur();
        $repartitionClient->setTitre('Provenance de nos études par type de Client (tous mandats)')
            ->setMethode('getRepartitionClientParNombreDEtude');
        
        // Provenance du chiffre d'Affaires (tous mandats) par type de client
        // TODO : selectionner un mandat default getMaxMandat -1 = tous les mandats
        $repartitionCAClient = new Indicateur();
        $repartitionCAClient->setTitre('Provenance du chiffre d\'Affaires par type de Client (tous mandats)')
            ->setMethode('getRepartitionClientSelonChiffreAffaire');
        
        // Taux de fidélisation
        $clientFidel = new Indicateur();
        $clientFidel->setTitre('Taux de fidélisation')
            ->setMethode('getPartClientFidel');
        
        $stats = $this->getStatistiques();
        

        $this->indicateursCollection
            ->setIndicateurs($chiffreAffaires, 'Treso')
            ->setIndicateurs($chiffreAffairesMandat, 'Treso')
            ->setIndicateurs($sortieNFFA, 'Treso')
            ->setIndicateurs($repartitionSortieNFFA, 'Treso')
            ->setIndicateurs($ressourcesHumaines, 'Gestion')
            ->setIndicateurs($membresParPromo, 'Gestion')
            ->setIndicateurs($membres, 'Gestion')
            ->setIndicateurs($repartitionClient, 'Com')
            ->setIndicateurs($repartitionCAClient, 'Com')
            ->setIndicateurs($clientFidel, 'Com')
            ->setIndicateurs($tauxAvenant, 'Suivi')
            ->setIndicateurs($nombreEtudes, 'Suivi')
            ->setIndicateurs($retardSurEtude , 'Suivi')
            ->setIndicateurs($nombreDeFormationsParMandat, 'Rfp')
            ->setIndicateurs($presenceAuxFormationsTimed, 'Rfp');

        //Enregistrement Cross Requete des Méthodes tolérées
        $_SESSION['autorizedMethods'] = $this->indicateursCollection->getAutorizedMethods();

        return $this->render('mgateStatBundle:Indicateurs:index.html.twig', array('indicateursSuivi' => $this->indicateursCollection->getIndicateurs('Suivi'),
                'indicateursRfp' => $this->indicateursCollection->getIndicateurs('Rfp'),
                'indicateursGestion' => $this->indicateursCollection->getIndicateurs('Gestion'),
                'indicateursCom' => $this->indicateursCollection->getIndicateurs('Com'),
                'indicateursTreso' => $this->indicateursCollection->getIndicateurs('Treso'),
                'stats' => $stats,
            ));
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function debugAction($get) {
        $indicateur = new Indicateur();
        $indicateur->setTitre($get)
            ->setMethode($get);
        return $this->render('mgateStatBundle:Indicateurs:debug.html.twig', array('indicateur' => $indicateur,
            ));
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function ajaxAction() {
        $request = $this->get('request');

        if ($request->getMethod() == 'GET') {
            $chartMethode = $request->query->get('chartMethode');
            if (in_array($chartMethode, $this->indicateursCollection->getAutorizedMethods()))
                return $this->$chartMethode();
        }
        return new \Symfony\Component\HttpFoundation\Response('<!-- Chart ' . $chartMethode . ' does not exist. -->');
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    // NB On se base pas sur les numéro mais les dates de signature CC !
    private function getRetardParMandat() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();

        $Ccs = $em->getRepository('mgateSuiviBundle:Cc')->findBy(array(), array('dateSignature' => 'asc'));

        /* Initialisation */
        $nombreJoursParMandat = array();
        $nombreJoursAvecAvenantParMandat = array();

        $maxMandat = $etudeManager->getMaxMandatCc();

        for ($i = 0; $i <= $maxMandat; $i++)
            $nombreJoursParMandat[$i] = 0;
        for ($i = 0; $i <= $maxMandat; $i++)
            $nombreJoursAvecAvenantParMandat[$i] = 0;
        /*         * *************** */

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();
            $signee = $etude->getStateID() == STATE_ID_EN_COURS_X
                || $etude->getStateID() == STATE_ID_TERMINEE_X;

            if ($dateSignature && $signee) {
                $idMandat = $etudeManager->dateToMandat($dateSignature);
                if($etude->getDelai()){
                    $nombreJoursParMandat[$idMandat] +=  $etude->getDelai(false)->days;
                    $nombreJoursAvecAvenantParMandat[$idMandat] += $etude->getDelai(true)->days;
                }
            }
        }

        $data = array();
        $categories = array();
        foreach ($nombreJoursParMandat as $idMandat => $datas) {
            if ($datas > 0) {
                $categories[] = $idMandat;
                $data[] = array('y' => 100 * ($nombreJoursAvecAvenantParMandat[$idMandat] - $datas) / $datas, 'nombreEtudes' => $datas, 'nombreEtudesAvecAv' => $nombreJoursAvecAvenantParMandat[$idMandat] - $datas);
            }
        }
        $series = array(array("name" => "Nombre de jour de retard / nombre de jour travaillés", "colorByPoint" => true, "data" => $data));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');

        /*         * ***********************
         * DATAS
         */
        $series = array(array("name" => "Nombre de jour de retard / nombre de jour travaillés", "colorByPoint" => true, "data" => $data));
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $ob->yAxis->max(100);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Reatard par Mandat');
        $ob->yAxis->title(array('text' => "Taux (%)", 'style' => $style));
        $ob->xAxis->title(array('text' => "Mandat", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('Les études ont duré en moyenne {point.y:.2f} % de plus que prévu<br/>avec {point.nombreEtudesAvecAv} jours de retard sur {point.nombreEtudes} jours travaillés');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    // NB On se base pas sur les numéro mais les dates de signature CC !
    private function getNombreEtudes() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();

        $Ccs = $em->getRepository('mgateSuiviBundle:Cc')->findBy(array(), array('dateSignature' => 'asc'));

        /* Initialisation */
        $nombreEtudesParMandat = array();

        $maxMandat = $etudeManager->getMaxMandatCc();

        for ($i = 0; $i <= $maxMandat; $i++)
            $nombreEtudesParMandat[$i] = 0;
        /*         * *************** */

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();
            $signee = $etude->getStateID() == STATE_ID_EN_COURS_X
                || $etude->getStateID() == STATE_ID_TERMINEE_X;

            if ($dateSignature && $signee) {
                $idMandat = $etudeManager->dateToMandat($dateSignature);
                $nombreEtudesParMandat[$idMandat] ++;
            }
        }

        $data = array();
        $categories = array();
        foreach ($nombreEtudesParMandat as $idMandat => $datas) {
            if ($datas > 0) {
                $categories[] = $idMandat;
                $data[] = array('y' => $datas,);
            }
        }
        $series = array(array("name" => "Nombre d'études par mandat", "colorByPoint" => true, "data" => $data));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');
        $ob->yAxis->allowDecimals(false);

        /*         * ***********************
         * DATAS
         */
        $series = array(array("name" => "Nombre d'études par mandat", "colorByPoint" => true, "data" => $data));
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Nombre d\'études par mandat');
        $ob->yAxis->title(array('text' => "Nombre", 'style' => $style));
        $ob->xAxis->title(array('text' => "Mandat", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} études');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getRepartitionSorties() {
        $em = $this->getDoctrine()->getManager();
        $mandat = $etudeManager = $this->get('mgate.etude_manager')->getMaxMandatCc();
        
        $nfs = $em->getRepository('mgateTresoBundle:NoteDeFrais')->findBy(array('mandat' => $mandat));
        $bvs = $em->getRepository('mgateTresoBundle:BV')->findBy(array('mandat' => $mandat));
        
        
        /* Initialisation */
        $comptes = array();
        $comptes['Honoraires BV'] = 0;
        $comptes['URSSAF'] = 0;
        $montantTotal = 0;
        foreach ($nfs as $nf){
            foreach ($nf->getDetails() as $detail){
                $compte = $detail->getCompte();
                if($compte != NULL){
                    $compte = $detail->getCompte()->getLibelle();
                    $montantTotal += $detail->getMontantHT();
                    if(array_key_exists($compte, $comptes))
                        $comptes[$compte] += $detail->getMontantHT();
                    else
                        $comptes[$compte] = $detail->getMontantHT();
                }
            }
        }
        
        foreach ($bvs as $bv){
            $comptes['Honoraires BV'] += $bv->getRemunerationBrute();
            $comptes['URSSAF'] += $bv->getPartJunior();
            $montantTotal += $bv->getRemunerationBrute() + $bv->getPartJunior();
        }
        
        ksort($comptes);
        $data = array();
        foreach ($comptes as $compte => $montantHT){
            $data[] = array($compte, 100 * $montantHT / $montantTotal);
        }
        
        $series = array(array('type' => 'pie', 'name' => 'Répartition des dépenses', 'data' => $data, 'Dépenses totale' => $montantTotal));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // Plot Options
        $ob->plotOptions->pie(array('allowPointSelect' => true, 'cursor' => 'pointer', 'showInLegend' => true, 'dataLabels' => array('enabled' => false)));

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);

        /*         * ***********************
         * STYLE
         */
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->credits->enabled(false);


        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text("Répartition des dépenses selon les comptes comptables (Mandat en cours)");
        $ob->tooltip->pointFormat('{point.percentage:.1f} %');

        /*
         *
         * *********************** */
        

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getSortie() {
        $em = $this->getDoctrine()->getManager();
        
        $sortiesParMandat = $em->getRepository('mgateTresoBundle:NoteDeFrais')->findAllByMandat();
        $bvsParMandat = $em->getRepository('mgateTresoBundle:BV')->findAllByMandat();
        
        
        $data = array();
        $categories = array();

        $comptes = array();
        $comptes['Honoraires BV'] = array();
        $comptes['URSSAF'] = array();
        $mandats = array();
        ksort($sortiesParMandat); // Trie selon les mandats
        foreach ($sortiesParMandat as $mandat => $nfs) { // Pour chaque Mandat
            $mandats[] = $mandat;
            foreach ($nfs as $nf){ // Pour chaque NF d'un mandat
                foreach ($nf->getDetails() as $detail){ // Pour chaque détail d'une NF
                    $compte = $detail->getCompte();
                    if($compte != NULL){
                        $compte = $detail->getCompte()->getLibelle();
                        if(array_key_exists($compte, $comptes)){
                            if(array_key_exists($mandat, $comptes[$compte]))
                                $comptes[$compte][$mandat] += $detail->getMontantHT();
                            else
                                $comptes[$compte][$mandat] = $detail->getMontantHT();
                        }
                        else
                            $comptes[$compte] = array($mandat => $detail->getMontantHT());                            
                    }
                }
            }
        }
        foreach ($bvsParMandat as $mandat => $bvs) { // Pour chaque Mandat
            if(!in_array($mandat,$mandats)) $mandats[] = $mandat;
            $comptes['Honoraires BV'][$mandat] = 0;
            $comptes['URSSAF'][$mandat] = 0;
            foreach ($bvs as $bv){// Pour chaque BV d'un mandat
                $comptes['Honoraires BV'][$mandat] += $bv->getRemunerationBrute();
                $comptes['URSSAF'][$mandat] += $bv->getPartJunior();
            }
        }
                
        $series = array();
        ksort($mandats);
        ksort($comptes);
        foreach ($comptes as $libelle => $compte){
            $data = array();
            foreach ($mandats as $mandat){
                if(array_key_exists($mandat, $compte))
                    $data[] = (float) $compte[$mandat];
                else
                    $data[] = 0;
            }    
            $series[] = array('name' => $libelle, "data" => $data);
        }
        
        foreach ($mandats as $mandat)
             $categories[] = 'Mandat ' . $mandat; 

        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');
        $ob->plotOptions->column(array('stacking' => 'normal'));

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $ob->yAxis->allowDecimals(false);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(true);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Montant HT des dépenses');
        $ob->yAxis->title(array('text' => "Montant (€)", 'style' => $style));
        $ob->xAxis->title(array('text' => "Mandat", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} € HT');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getPartClientFidel() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findAll();

        
        $clients = array();
        foreach ($etudes as $etude) {
            if ($etude->getStateID() == STATE_ID_EN_COURS_X || $etude->getStateID() == STATE_ID_TERMINEE_X) { 
                $clientID = $etude->getProspect()->getId();
                if(key_exists($clientID, $clients))
                    $clients[$clientID]++;
                else
                    $clients[$clientID] = 1;
            }
        }
        
        $repartitions = array();
        $nombreClient = count($clients);
        foreach ($clients as $clientID => $nombreEtude){
            if(key_exists($nombreEtude,  $repartitions))
                $repartitions[$nombreEtude]++;
            else
                $repartitions[$nombreEtude] = 1;
        }
        
        /* Initialisation */
        $data = array();
        ksort($repartitions);
        foreach ($repartitions as $occ => $nbr ){
            $data[] = array($occ == 1 ? "$nbr Nouveaux clients" : "$nbr Anciens clients ($occ études)", 100 * $nbr / $nombreClient);
        }
        
        $series = array(array('type' => 'pie', 'name' => 'Taux de fidélisation', 'data' => $data, 'Nombre de client' => $nombreClient));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // Plot Options
        $ob->plotOptions->pie(array('allowPointSelect' => true, 'cursor' => 'pointer', 'showInLegend' => true, 'dataLabels' => array('enabled' => false)));

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);

        /*         * ***********************
         * STYLE
         */
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->credits->enabled(false);


        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text("Taux de fidélisation (% de clients ayant demandé plusieurs études)");
        $ob->tooltip->pointFormat('{point.percentage:.1f} %');

        /*
         *
         * *********************** */

        
        

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getNombreDePresentFormationsTimed() {
        $em = $this->getDoctrine()->getManager();
        $formationsParMandat = $em->getRepository('mgateFormationBundle:Formation')->findAllByMandat();

        $maxMandat = max(array_keys($formationsParMandat));
        $mandats = array();

        foreach ($formationsParMandat as $mandat => $formations) {
            foreach($formations as $formation){
                if($formation->getDateDebut()){
                    $interval = new \DateInterval('P' . ($maxMandat - $mandat) . 'Y');
                    $dateDecale = clone $formation->getDateDebut();
                    $dateDecale->add($interval);
                    $mandats[$mandat][] = array(
                        "x" => $dateDecale->getTimestamp() * 1000,
                        "y" => count($formation->getMembresPresents()), "name" => $formation->getTitre(),
                        'date' => $dateDecale->format('d/m/Y'),
                    );   
                }
            }
        }
        
        $series = array();
        foreach ($mandats as $mandat => $data) {
            $series[] = array("name" => "Mandat " . $mandat, "data" => $data);
        }

        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->global->useUTC(false);

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);
        $ob->xAxis->type('datetime');
        $ob->xAxis->dateTimeLabelFormats(array('month' => "%b"));

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $ob->yAxis->allowDecimals(false);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Nombre de présents aux formations');
        $ob->yAxis->title(array('text' => "Nombre de présents", 'style' => $style));
        $ob->xAxis->title(array('text' => "Date", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} présent le {point.date}<br />{point.name}');
        $ob->legend->layout('vertical');
        $ob->legend->y(40);
        $ob->legend->x(90);
        $ob->legend->verticalAlign('top');
        $ob->legend->reversed(true);
        $ob->legend->align('left');
        $ob->legend->backgroundColor('#FFFFFF');
        $ob->legend->itemStyle($style);
        $ob->plotOptions->series(array('lineWidth' => 5, 'marker' => array('radius' => 8)));

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getNombreFormationsParMandat() {
        $em = $this->getDoctrine()->getManager();
        
        $formationsParMandat = $em->getRepository('mgateFormationBundle:Formation')->findAllByMandat();

        $data = array();
        $categories = array();

        ksort($formationsParMandat); // Tire selon les promos
        foreach ($formationsParMandat as $mandat => $formations) {
            $data[] = count($formations);
            $categories[] = 'Mandat ' . $mandat;
        }
        $series = array(array("name" => "Nombre de formations", "colorByPoint" => true, "data" => $data));

        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $ob->yAxis->allowDecimals(false);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Nombre de formations théorique par mandat');
        $ob->yAxis->title(array('text' => "Nombre de formations", 'style' => $style));
        $ob->xAxis->title(array('text' => "Mandat", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y}');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    
    /**
     * @Secure(roles="ROLE_CA")
     */
    // NB On se base pas sur les numéro mais les dates de signature CC !
    private function getTauxDAvenantsParMandat() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();

        $Ccs = $em->getRepository('mgateSuiviBundle:Cc')->findBy(array(), array('dateSignature' => 'asc'));

        /* Initialisation */
        $nombreEtudesParMandat = array();
        $nombreEtudesAvecAvenantParMandat = array();

        $maxMandat = $etudeManager->getMaxMandatCc();

        for ($i = 0; $i <= $maxMandat; $i++)
            $nombreEtudesParMandat[$i] = 0;
        for ($i = 0; $i <= $maxMandat; $i++)
            $nombreEtudesAvecAvenantParMandat[$i] = 0;
        /*         * *************** */

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();
            $signee = $etude->getStateID() == STATE_ID_EN_COURS_X
                || $etude->getStateID() == STATE_ID_TERMINEE_X;

            if ($dateSignature && $signee) {
                $idMandat = $etudeManager->dateToMandat($dateSignature);

                $nombreEtudesParMandat[$idMandat] ++;
                if(count($etude->getAvs()->toArray()))
                    $nombreEtudesAvecAvenantParMandat[$idMandat] ++;
            }
        }

        $data = array();
        $categories = array();
        foreach ($nombreEtudesParMandat as $idMandat => $datas) {
            if ($datas > 0) {
                $categories[] = $idMandat;
                $data[] = array('y' => 100 * $nombreEtudesAvecAvenantParMandat[$idMandat] / $datas, 'nombreEtudes' => $datas, 'nombreEtudesAvecAv' => $nombreEtudesAvecAvenantParMandat[$idMandat]);
            }
        }
        $series = array(array("name" => "Taux d'avenant par Mandat", "colorByPoint" => true, "data" => $data));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');

        /*         * ***********************
         * DATAS
         */
        $series = array(array("name" => "Taux d'avenant", "colorByPoint" => true, "data" => $data));
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $ob->yAxis->max(100);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Taux d\'avenant par Mandat');
        $ob->yAxis->title(array('text' => "Taux (%)", 'style' => $style));
        $ob->xAxis->title(array('text' => "Mandat", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y:.2f} %<br/>avec {point.nombreEtudesAvecAv} sur {point.nombreEtudes} études');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getRepartitionClientSelonChiffreAffaire() {
        $em = $this->getDoctrine()->getManager();
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findAll();


        $chiffreDAffairesTotal = 0;
        
        $repartitions = array();

        foreach ($etudes as $etude) {
            if ($etude->getStateID() == STATE_ID_EN_COURS_X || $etude->getStateID() == STATE_ID_TERMINEE_X) {                
                $type = $etude->getProspect()->getEntiteToString();
                $CA = $etude->getMontantHT();
                $chiffreDAffairesTotal += $CA;
                array_key_exists($type, $repartitions) ? $repartitions[$type] += $CA : $repartitions[$type] = $CA;
            }
        }


        $data = array();
        $categories = array();
        foreach ($repartitions as $type => $CA) {
            if ($type == NULL)
                $type = "Autre";
            $data[] = array($type, round($CA / $chiffreDAffairesTotal * 100, 2));
        }

        $series = array(array('type' => 'pie', 'name' => 'Provenance de nos études par type de Client (tous mandats)', 'data' => $data, 'CA Total' => $chiffreDAffairesTotal));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // Plot Options
        $ob->plotOptions->pie(array('allowPointSelect' => true, 'cursor' => 'pointer', 'showInLegend' => true, 'dataLabels' => array('enabled' => false)));

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);

        /*         * ***********************
         * STYLE
         */
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->credits->enabled(false);


        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text("Répartition du CA selon le type de Client ($chiffreDAffairesTotal € CA)");
        $ob->tooltip->pointFormat('{point.percentage:.1f} %');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    
    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getRepartitionClientParNombreDEtude() {
        $em = $this->getDoctrine()->getManager();
        $etudes = $em->getRepository('mgateSuiviBundle:Etude')->findAll();


        $nombreClient = 0;
        $repartitions = array();

        foreach ($etudes as $etude) {
            if ($etude->getStateID() == STATE_ID_EN_COURS_X || $etude->getStateID() == STATE_ID_TERMINEE_X) {
                $nombreClient++;
                $type = $etude->getProspect()->getEntiteToString();
                array_key_exists($type, $repartitions) ? $repartitions[$type]++ : $repartitions[$type] = 1;
            }
        }


        $data = array();
        $categories = array();
        foreach ($repartitions as $type => $nombre) {
            if ($type == NULL)
                $type = "Autre";
            $data[] = array($type, round($nombre / $nombreClient * 100, 2));
        }

        $series = array(array('type' => 'pie', 'name' => 'Provenance des études par type de Client (tous mandats)', 'data' => $data, 'nombreClient' => $nombreClient));


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // Plot Options
        $ob->plotOptions->pie(array('allowPointSelect' => true, 'cursor' => 'pointer', 'showInLegend' => true, 'dataLabels' => array('enabled' => false)));

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);

        /*         * ***********************
         * STYLE
         */
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->credits->enabled(false);


        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text("Provenance des études par type de Client ($nombreClient Etudes)");
        $ob->tooltip->pointFormat('{point.percentage:.1f} %');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    function cmp($a, $b) {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] < $b['date']) ? -1 : 1;
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getNombreMembres() {
        $em = $this->getDoctrine()->getManager();
        $mandats = $em->getRepository('mgatePersonneBundle:Mandat')->getCotisantMandats();


        $promos = array();
        $cumuls = array();
        $dates = array();
        foreach ($mandats as $mandat) {
            if ($membre = $mandat->getMembre()) {
                $p = $membre->getPromotion();
                if(!in_array($p, $promos)) $promos[] = $p;
                $dates[] = array('date' => $mandat->getDebutMandat(), 'type' => '1', 'promo' => $p);
                $dates[] = array('date' => $mandat->getFinMandat(), 'type' => '-1', 'promo' => $p);
            }
        }
        sort($promos);
        usort($dates, array($this, 'cmp'));

        foreach ($dates as $date) {
            $d = $date['date']->format('m/y');
            $p = $date['promo'];
            $t = $date['type'];
            foreach ($promos as $promo) {
                if (!array_key_exists($promo, $cumuls))
                    $cumuls[$promo] = array();
                $cumuls[$promo][$d] = (array_key_exists($d, $cumuls[$promo]) ? $cumuls[$promo][$d] : (end($cumuls[$promo]) ? end($cumuls[$promo]) : 0));
            }
            $cumuls[$p][$d] += $t;
        }

        $series = array();
        $categories = array_keys($cumuls[$promos[0]]);
        foreach (array_reverse($promos) as $promo) {
            $series[] = array('name' => 'P' . $promo, 'data' => array_values($cumuls[$promo]));
        }


        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('area');
        $ob->chart->zoomType('x');
        $ob->plotOptions->area(array('stacking' => 'normal'));

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $ob->yAxis->allowDecimals(false);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style, 'rotation' => -45));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Nombre de membre');
        $ob->yAxis->title(array('text' => "Nombre de membre", 'style' => $style));
        $ob->xAxis->title(array('text' => "Promotion", 'style' => $style));
        $ob->tooltip->shared(true);
        $ob->tooltip->valueSuffix(' cotisants');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getMembresParPromo() {
        $em = $this->getDoctrine()->getManager();
        $membres = $em->getRepository('mgatePersonneBundle:Membre')->findAll();

        $promos = array();

        foreach ($membres as $membre) {
            $p = $membre->getPromotion();
            if ($p)
                array_key_exists($p, $promos) ? $promos[$p]++ : $promos[$p] = 1;
        }

        $data = array();
        $categories = array();


        ksort($promos); // Tire selon les promos
        foreach ($promos as $promo => $nombre) {
            $data[] = $nombre;
            $categories[] = 'P' . $promo;
        }
        $series = array(array("name" => "Membres", "colorByPoint" => true, "data" => $data));

        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Nombre de membres par Promotion');
        $ob->yAxis->title(array('text' => "Nombre de membres", 'style' => $style));
        $ob->xAxis->title(array('text' => "Promotion", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y}');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getIntervenantsParPromo() {
        $em = $this->getDoctrine()->getManager();
        $intervenants = $em->getRepository('mgatePersonneBundle:Membre')->getIntervenantsParPromo();

        $promos = array();

        foreach ($intervenants as $intervenant) {
            $p = $intervenant->getPromotion();
            if ($p)
                array_key_exists($p, $promos) ? $promos[$p]++ : $promos[$p] = 1;
        }

        $data = array();
        $categories = array();
        foreach ($promos as $promo => $nombre) {
            $data[] = $nombre;
            $categories[] = 'P' . $promo;
        }
        $series = array(array("name" => "Intervenants", "colorByPoint" => true, "data" => $data));

        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');

        /*         * ***********************
         * DATAS
         */
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Nombre d\'intervenants par Promotion');
        $ob->yAxis->title(array('text' => "Nombre d'intervenants", 'style' => $style));
        $ob->xAxis->title(array('text' => "Promotion", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y}');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getCAM() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();

        $Ccs = $this->getDoctrine()->getManager()->getRepository('mgateSuiviBundle:Cc')->findBy(array(), array('dateSignature' => 'asc'));

        /* Initialisation */
        $mandats = array();
        $cumuls = array();
        $cumulsJEH = array();
        $cumulsFrais = array();

        $maxMandat = $etudeManager->getMaxMandatCc();

        for ($i = 0; $i <= $maxMandat; $i++)
            $cumuls[$i] = 0;
        for ($i = 0; $i <= $maxMandat; $i++)
            $cumulsJEH[$i] = 0;
        for ($i = 0; $i <= $maxMandat; $i++)
            $cumulsFrais[$i] = 0;
        /*         * *************** */

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();
            $signee = $etude->getStateID() == STATE_ID_EN_COURS_X
                || $etude->getStateID() == STATE_ID_TERMINEE_X;

            if ($dateSignature && $signee) {
        
                $idMandat = $etudeManager->dateToMandat($dateSignature);

                $cumuls[$idMandat] += $etude->getMontantHT();
                $cumulsJEH[$idMandat] += $etude->getNbrJEH();
                $cumulsFrais[$idMandat] += $etude->getFraisDossier();
            }
        }


        $data = array();
        $categories = array();
        foreach ($cumuls as $idMandat => $datas) {
            if ($datas > 0) {
                $categories[] = $idMandat;
                $data[] = array('y' => $datas, 'JEH' => $cumulsJEH[$idMandat], 'moyJEH' => ($datas - $cumulsFrais[$idMandat]) / $cumulsJEH[$idMandat]);
            }
        }

        /*         * ***********************
         * CHART
         */
        $ob = new Highchart();
        $ob->chart->renderTo(__FUNCTION__);
        // OTHERS
        $ob->chart->type('column');

        /*         * ***********************
         * DATAS
         */
        $series = array(
            array(
                "name" => "CA Signé",
                "colorByPoint" => true, 
                "data" => $data,
                "dataLabels" => array(
                    "enabled" => true,
                    "rotation" => -90,
                    "align" => "right",
                    'format' => '{point.y} €',
                    "style" => array(
                        'color' => '#FFFFFF', 
                        "fontSize"  => '20px',
                        "fontFamily" => 'Verdana, sans-serif',
                        "textShadow" => '0 0 3px black',),
                    "y" => 25,
                    
                    ),
                )
            );
        $ob->series($series);
        $ob->xAxis->categories($categories);

        /*         * ***********************
         * STYLE
         */
        $ob->yAxis->min(0);
        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);

        /*         * ***********************
         * TEXTS AND LABELS
         */
        $ob->title->text('Évolution du chiffre d\'affaires signé cumulé par mandat');
        $ob->yAxis->title(array('text' => "CA (€)", 'style' => $style));
        $ob->xAxis->title(array('text' => "Mandat", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} €<br/>en {point.JEH} JEH<br/>soit {point.moyJEH:.2f} €/JEH');

        /*
         *
         * *********************** */

        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    /*
     *  REGION OLD
     */

    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getCA() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $Ccs = $this->getDoctrine()->getManager()->getRepository('mgateSuiviBundle:Cc')->findBy(array(), array('dateSignature' => 'asc'));

        //$data = array();
        $mandats = array();
        $maxMandat = $etudeManager->getMaxMandatCc();

        $cumuls = array();
        for ($i = 0; $i <= $maxMandat; $i++)
            $cumuls[$i] = 0;

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();
            $signee = $etude->getStateID() == STATE_ID_EN_COURS_X
                || $etude->getStateID() == STATE_ID_TERMINEE_X;

            if ($dateSignature && $signee) {
                $idMandat = $etudeManager->dateToMandat($dateSignature);

                $cumuls[$idMandat] += $etude->getMontantHT();

                $interval = new \DateInterval('P' . ($maxMandat - $idMandat) . 'Y');
                $dateDecale = clone $dateSignature;
                $dateDecale->add($interval);

                $mandats[$idMandat][]
                    = array("x" => $dateDecale->getTimestamp() * 1000,
                    "y" => $cumuls[$idMandat], "name" => $etude->getReference() . " - " . $etude->getNom(),
                    'date' => $dateDecale->format('d/m/Y'),
                    'prix' => $etude->getMontantHT());
            }
        }



        // Chart
        $series = array();
        foreach ($mandats as $idMandat => $data) {
            //if($idMandat>=4)
            $series[] = array("name" => "Mandat " . $idMandat . " - " . $etudeManager->mandatToString($idMandat), "data" => $data);
        }

        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');

        $ob = new Highchart();
        $ob->global->useUTC(false);

        $ob->chart->renderTo(__FUNCTION__);  // The #id of the div where to render the chart
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->title->text('Évolution par mandat du chiffre d\'affaire signé cumulé');
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->title(array('text' => "Date", 'style' => $style));
        $ob->xAxis->type('datetime');
        $ob->xAxis->dateTimeLabelFormats(array('month' => "%b"));
        $ob->yAxis->min(0);
        $ob->yAxis->title(array('text' => "Chiffre d'Affaire signé cumulé", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->tooltip->pointFormat('{point.y} le {point.date}<br />{point.name} à {point.prix} €');
        $ob->credits->enabled(false);
        $ob->legend->floating(true);
        $ob->legend->layout('vertical');
        $ob->legend->y(-60);
        $ob->legend->x(-10);
        $ob->legend->verticalAlign('bottom');
        $ob->legend->reversed(true);
        $ob->legend->align('right');
        $ob->legend->backgroundColor('#F6F6F6');
        $ob->legend->itemStyle($style);
        $ob->plotOptions->series(
            array(                
                'lineWidth' => 3,
                'marker' => array('radius' => 6),
                )
            );
        /*
        $ob->plotOptions->line(
            array(
                'dataLabels' => array(
                    'enabled' => true,
                    'align' => 'left',
                    'verticalAlign' => 'center',
                    'x' => 5, 
                    'format' => '{point.prix} €'
                    
                    )
                )
            );*/
        
  
                
                
        $ob->series($series);

        //return $this->render('mgateStatBundle:Default:ca.html.twig', array(
        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    private function getRh() {
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();
        $etude = new \mgate\SuiviBundle\Entity\Etude;
        $missions = $this->getDoctrine()->getManager()->getRepository('mgateSuiviBundle:Mission')->findBy(array(), array('debutOm' => 'asc'));

        //$data = array();
        $mandats = array();
        $maxMandat = $etudeManager->getMaxMandatCc();

        $cumuls = array();
        for ($i = 0; $i <= $maxMandat; $i++)
            $cumuls[$i] = 0;

        $mandats[1] = array();

        //Etape 1 remplir toutes les dates
        foreach ($missions as $mission) {
            $etude = $mission->getEtude();
            $dateDebut = $mission->getdebutOm();
            $dateFin = $mission->getfinOm();

            if ($dateDebut && $dateFin) {
                $idMandat = $etudeManager->dateToMandat($dateDebut);

                $cumuls[0]++;

                //$interval = new \DateInterval('P' . ($maxMandat - $idMandat) . 'Y');
                $dateDebutDecale = clone $dateDebut;
                //$dateDebutDecale->add($interval);
                $dateFinDecale = clone $dateFin;
                //$dateFinDecale->add($interval);

                $addDebut = true;
                $addFin = true;
                foreach ($mandats[1] as $datePoint) {
                    if (($dateDebutDecale->getTimestamp() * 1000) == $datePoint['x'])
                        $addDebut = false;
                    if (($dateFinDecale->getTimestamp() * 1000) == $datePoint['x'])
                        $addFin = false;
                }

                if ($addDebut) {
                    $mandats[1][]
                        = array("x" => $dateDebutDecale->getTimestamp() * 1000,
                        "y" => 0/* $cumuls[0] */, "name" => $etude->getReference() . " + " . $etude->getNom(),
                        'date' => $dateDebutDecale->format('d/m/Y'),
                        'prix' => $etude->getMontantHT());
                }
                if ($addFin) {
                    $mandats[1][]
                        = array("x" => $dateFinDecale->getTimestamp() * 1000,
                        "y" => 0/* $cumuls[0] */, "name" => $etude->getReference() . " - " . $etude->getNom(),
                        'date' => $dateDebutDecale->format('d/m/Y'),
                        'prix' => $etude->getMontantHT());
                }
            }
        }

        //Etapes 2 trie dans l'ordre
        $callback = function($a, $b) use($mandats) {
                return $mandats[1][$a]['x'] > $mandats[1][$b]['x'];
            };
        uksort($mandats[1], $callback);
        foreach ($mandats[1] as $entree)
            $mandats[2][] = $entree;
        $mandats[1] = array();

        //Etapes 3 ++ --
        foreach ($missions as $mission) {
            $etude = $mission->getEtude();
            $dateFin = $mission->getfinOm();
            $dateDebut = $mission->getdebutOm();

            if ($dateDebut && $dateFin) {
                $idMandat = $etudeManager->dateToMandat($dateFin);

                //$interval2 = new \DateInterval('P'.($maxMandat-$idMandat).'Y');
                $dateDebutDecale = clone $dateDebut;
                //$dateDebutDecale->add($interval2);
                $dateFinDecale = clone $dateFin;
                //$dateFinDecale->add($interval2);

                foreach ($mandats[2] as &$entree) {
                    if ($entree['x'] >= $dateDebutDecale->getTimestamp() * 1000 && $entree['x'] < $dateFinDecale->getTimestamp() * 1000) {
                        $entree['y']++;
                    }
                }
            }
        }

        // Chart
        $series = array();
        foreach ($mandats as $idMandat => $data) {
            //if($idMandat>=4)
            $series[] = array("name" => "Mandat " . $idMandat . " - " . $etudeManager->mandatToString($idMandat), "data" => $data);
        }

        $style = array('color' => '#000000', 'fontWeight' => 'bold', 'fontSize' => '16px');

        $ob = new Highchart();
        $ob->global->useUTC(false);



        //WARN :::

        $ob->chart->renderTo('getRh');  // The #id of the div where to render the chart
        ///
        $ob->chart->type("spline");
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->labels(array('style' => $style));
        $ob->title->text("Évolution par mandat du nombre d'intervenant");
        $ob->title->style(array('fontWeight' => 'bold', 'fontSize' => '20px'));
        $ob->xAxis->title(array('text' => "Date", 'style' => $style));
        $ob->xAxis->type('datetime');
        $ob->xAxis->dateTimeLabelFormats(array('month' => "%b"));
        $ob->yAxis->min(0);
        $ob->yAxis->title(array('text' => "Nombre d'intervenant", 'style' => $style));
        $ob->tooltip->headerFormat('<b>{series.name}</b><br />');
        $ob->credits->enabled(false);
        $ob->legend->floating(true);
        $ob->legend->layout('vertical');
        $ob->legend->y(40);
        $ob->legend->x(90);
        $ob->legend->verticalAlign('top');
        $ob->legend->reversed(true);
        $ob->legend->align('left');
        $ob->legend->backgroundColor('#FFFFFF');
        $ob->legend->itemStyle($style);
        $ob->plotOptions->series(array('lineWidth' => 5, 'marker' => array('radius' => 8)));
        $ob->series($series);

        //return $this->render('mgateStatBundle:Default:ca.html.twig', array(
        return $this->render('mgateStatBundle:Indicateurs:Indicateur.html.twig', array(
                'chart' => $ob
            ));
    }
    
    private function getStatistiques(){
        $etudeManager = $this->get('mgate.etude_manager');
        $em = $this->getDoctrine()->getManager();
        
        
        
        return array('Pas de données' => 'A venir');
    }

}

