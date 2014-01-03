<?php

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

    public function getTitre() {
        return $this->titre;
    }

    public function getMethode() {
        return $this->methode;
    }

    public function setTitre($x) {
        $this->titre = $x;
        return $this;
    }

    public function setMethode($x) {
        $this->methode = $x;
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

        $this->indicateursCollection
            ->setIndicateurs($chiffreAffaires, 'Treso')
            ->setIndicateurs($chiffreAffairesMandat, 'Treso')
            ->setIndicateurs($ressourcesHumaines, 'Gestion')
            ->setIndicateurs($membresParPromo, 'Gestion')
            ->setIndicateurs($membres, 'Gestion')
            ->setIndicateurs($repartitionClient, 'Com')
            ->setIndicateurs($repartitionCAClient, 'Com')
            ->setIndicateurs($tauxAvenant, 'Suivi');

        //Enregistrement Cross Requete des Méthodes tolérées
        $_SESSION['autorizedMethods'] = $this->indicateursCollection->getAutorizedMethods();

        return $this->render('mgateStatBundle:Indicateurs:index.html.twig', array('indicateursSuivi' => $this->indicateursCollection->getIndicateurs('Suivi'),
                'indicateursRfp' => $this->indicateursCollection->getIndicateurs('Rfp'),
                'indicateursGestion' => $this->indicateursCollection->getIndicateurs('Gestion'),
                'indicateursCom' => $this->indicateursCollection->getIndicateurs('Com'),
                'indicateursTreso' => $this->indicateursCollection->getIndicateurs('Treso'),
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
                $CA = $this->get('mgate.etude_manager')->getTotalHT($etude);
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


        $promos = array(2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016);
        $cumuls = array();
        $dates = array();
        foreach ($mandats as $mandat) {
            if ($membre = $mandat->getMembre()) {
                $p = $membre->getPromotion();
                $dates[] = array('date' => $mandat->getDebutMandat(), 'type' => '1', 'promo' => $p);
                $dates[] = array('date' => $mandat->getFinMandat(), 'type' => '-1', 'promo' => $p);
            }
        }

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
        $categories = array_keys($cumuls[2009]);
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

        $maxMandat = $etudeManager->getMaxMandatCc();

        for ($i = 0; $i <= $maxMandat; $i++)
            $cumuls[$i] = 0;
        for ($i = 0; $i <= $maxMandat; $i++)
            $cumulsJEH[$i] = 0;
        /*         * *************** */

        foreach ($Ccs as $cc) {
            $etude = $cc->getEtude();
            $dateSignature = $cc->getDateSignature();
            $signee = $etude->getStateID() == STATE_ID_EN_COURS_X
                || $etude->getStateID() == STATE_ID_TERMINEE_X;

            if ($dateSignature && $signee) {
                $idMandat = $etudeManager->dateToMandat($dateSignature);

                $cumuls[$idMandat] += $etudeManager->getTotalHT($etude);
                $cumulsJEH[$idMandat] += $etudeManager->getNbrJEH($etude);
            }
        }


        $data = array();
        $categories = array();
        foreach ($cumuls as $idMandat => $datas) {
            if ($datas > 0) {
                $categories[] = $idMandat;
                $data[] = array('y' => $datas, 'JEH' => $cumulsJEH[$idMandat], 'moyJEH' => $datas / $cumulsJEH[$idMandat]);
            }
        }
        $series = array(array("name" => "Chiffre d\'Affaires Cummulé", "colorByPoint" => true, "data" => $data));


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
        $series = array(array("name" => "CA Signé", "colorByPoint" => true, "data" => $data));
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
        $ob->title->text('Évolution du chiffre d\'affaire signé cumulé par mandat');
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

                $cumuls[$idMandat] += $etudeManager->getTotalHT($etude);

                $interval = new \DateInterval('P' . ($maxMandat - $idMandat) . 'Y');
                $dateDecale = clone $dateSignature;
                $dateDecale->add($interval);

                $mandats[$idMandat][]
                    = array("x" => $dateDecale->getTimestamp() * 1000,
                    "y" => $cumuls[$idMandat], "name" => $etudeManager->getRefEtude($etude) . " - " . $etude->getNom(),
                    'date' => $dateDecale->format('d/m/Y'),
                    'prix' => $etudeManager->getTotalHT($etude));
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

        $ob->chart->renderTo('getCA');  // The #id of the div where to render the chart
        ///

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
                        "y" => 0/* $cumuls[0] */, "name" => $etudeManager->getRefEtude($etude) . " + " . $etude->getNom(),
                        'date' => $dateDebutDecale->format('d/m/Y'),
                        'prix' => $etudeManager->getTotalHT($etude));
                }
                if ($addFin) {
                    $mandats[1][]
                        = array("x" => $dateFinDecale->getTimestamp() * 1000,
                        "y" => 0/* $cumuls[0] */, "name" => $etudeManager->getRefEtude($etude) . " - " . $etude->getNom(),
                        'date' => $dateDebutDecale->format('d/m/Y'),
                        'prix' => $etudeManager->getTotalHT($etude));
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

}

