<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TraitementController extends Controller {

    private $SFD = '~'; //Start Field Delimiter
    private $EFD = '~~';

    /*
     * private SAD
     * private EAD
     */

    //Repétition des phases
    private function repeterPhase(&$templateXML, $nombrePhase) {


        $regexRepeatSTART = '<w:bookmarkStart w:id="\d+" w:name="repeatSTART\d?"/>\s*\S*<w:bookmarkEnd w:id="\d+"/>'; //Marqueur de début de repeat
        $regexRepeatEND = '<w:bookmarkStart w:id="\d+" w:name="repeatEND\d?"/>\s*\S*<w:bookmarkEnd w:id="\d+"/>'; //Marqueur de fin de repeat
        $regexpRepeat = '#' . $regexRepeatSTART . '(.*?)' . $regexRepeatEND . '#s'; // *? see ungreedy behavior //Expression régulière filtrage répétition /!\ imbrication interdite !

        $SFD = $this->SFD;
        $EFD = $this->EFD;
        $callback = function ($matches) use ($nombrePhase, $SFD, $EFD) { //Fonction de callback prétraitement de la zone à répéter
                    $outputString = "";


                    if (preg_match("#w:vMerge\s*/>#", $matches[1]))//Rowspan ?
                        $premiereLigne = preg_replace('#<w:vMerge\s*/>#', "<w:vMerge w:val=\"restart\"/>", $matches[1]);
                    else
                        $premiereLigne = $matches[1];

                    $outputString .= preg_replace('#' . $SFD . 'Phase_Index' . $EFD . '#U', "1", $premiereLigne);

                    for ($i = 2; $i <= $nombrePhase; $i++)
                        $outputString .= preg_replace('#' . $SFD . 'Phase_Index' . $EFD . '#U', "$i", $matches[1]);
                    return $outputString;
                };

        $templateXML = preg_replace_callback($regexpRepeat, $callback, $templateXML);

        return $templateXML;
    }

    //Remplissage des %champs%
    private function remplirChamps(&$templateXML, $fieldValues) {
        $SFD = $this->SFD;
        $EFD = $this->EFD;

        foreach ($fieldValues as $field => $values) {//Remplacement des champs
            if ($values != NULL) {
                if (is_int($values) || is_float($values)) //Formatage des nombres à la francaise
                    $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', $this->formaterNombre($values), $templateXML);
                else
                    $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', $this->nl2wbr($values), $templateXML);
            }
        }


        return $templateXML;
    }

    private function nl2wbr($input) {
        return preg_replace('#\\r\\n|\\n|\\r#', '<w:br />', $input);
    }

    //Accord en nombre
    /* ¤nombre|pluriel|singulier¤
     * ¤nombre|pluriel¤ (singulier = '')
     * ¤genre|feminin|masculin¤
     * ¤genre|fem¤ (masc = '')
     * >1 = Femme
     * 0||1 = Homme
     * ¤%sexe%|rendue|rendu¤
     * ¤%sexe%|e¤
     */
    private function accorder(&$templateXML) {
        $regexp = array(//Expression régulière filtrage répétition /!\ imbrication interdite !
            '#¤(\d+)\|([^¤.]*)\|([^¤.]*)¤#', //si deux args ¤3|ont|a¤
            '#¤(\d+)\|([^¤.]*)¤#', //si un arg : ¤3|s¤
        ); // Ou en un seul regex...

        $callback = function ($matches) {//Fonction de callback
                    if (isset($matches[3]))
                        return ($matches[1] > 1) ? $matches[2] : $matches[3];
                    else
                        return ($matches[1] > 1) ? $matches[2] : '';
                };

        $templateXML = preg_replace_callback($regexp, $callback, $templateXML);
        return $templateXML;
    }

    //effectu les "liaisons" le/l' la/l' ... pattern : µde|d'|variableµ
    private function liasons(&$templateXML) {
        $regexp = '#µ(.*?)\|([^µ.]*)\|([^µ.]*)µ#';
        
        $that = $this;
        $callback = function ($matches) use ($that) {//Fonction de callback
                    return (($that->commenceParUneVoyelle($matches[3]) == NULL) ? $matches[1] : $matches[2]) . $matches[3];
                };

        $templateXML = preg_replace_callback($regexp, $callback, $templateXML);
        return $templateXML;
        //commenceParUneVoyelle
    }

    //Traitement du template
    private function traiterTemplate($templateFullPath, $nombrePhase, $champs) {
        $templateXML = file_get_contents($templateFullPath); //récup contenu XML

        $this->repeterPhase($templateXML, $nombrePhase); //Répétion phase
        $this->remplirChamps($templateXML, $champs); //remplissage des champs + phases
        $this->accorder($templateXML); //Accord en nombre /!\ accord en genre ?
        $this->liasons($templateXML); //liaisons de d'

        return $templateXML;
    }

    //Vérification du fichier
    //if match % _ % then pasbien
    private function verifierTemplate($templateXML) {
        $SFD = $this->SFD;
        $EFD = $this->EFD;

        preg_match_all('#' . $SFD . '(.*?)' . $EFD . '#', $templateXML, $matches);

        return $matches[1];
    }

    public function commenceParUneVoyelle($mot) {
        return preg_match('#^[aeiouy]#', $mot);
    }

    private function nombreVersMois($m) {

        $m %= 12;

        $mois = NULL;
        switch ($m) {
            case 1:
                $mois = 'janvier';
                break;
            case 2:
                $mois = 'février';
                break;
            case 3:
                $mois = 'mars';
                break;
            case 4:
                $mois = 'avril';
                break;
            case 5:
                $mois = 'mai';
                break;
            case 6:
                $mois = 'juin';
                break;
            case 7:
                $mois = 'juillet';
                break;
            case 8:
                $mois = 'août';
                break;
            case 9:
                $mois = 'septembre';
                break;
            case 10:
                $mois = 'octobre';
                break;
            case 11:
                $mois = 'novembre';
                break;
            case 12:
                $mois = 'décembre';
                break;
            default:
                break;
        }
        return $mois;
    }

    private function getAllChamp($etude, $doc, $key = 0) {
        //External
        $etudeManager = $this->get('mgate.etude_manager');
        $converter = $this->get('mgate.conversionlettre');


        $phases = $etude->getPhases();
        $nombrePhase = (int) count($phases);



        //EtudeManager
        $Taux_TVA = (float) 19.6;
        $Montant_Total_JEH_HT = (float) $etudeManager->getTotalJEHHT($etude);
        $Montant_Total_Frais_HT = (float) $etude->getFraisDossier();
        $Montant_Total_Etude_HT = (float) $etudeManager->getTotalHT($etude);
        $Montant_Total_Etude_TTC = (float) $etudeManager->getTotalTTC($etude);
        $Part_TVA_Montant_Total_Etude = (float) $Taux_TVA * $Montant_Total_Etude_HT / 100;


        $Nbr_JEH = (int) $etudeManager->getNbrJEH($etude);
        $Nbr_JEH_Lettres = $converter->ConvNumberLetter($Nbr_JEH);

        if ($etudeManager->getDateLancement($etude))
            $Mois_Lancement = $this->nombreVersMois(intval($etudeManager->getDateLancement($etude)->format('m')));
        else
            $Mois_Lancement = NULL;
        if ($etudeManager->getDateFin($etude)) {
            $Mois_Fin = $this->nombreVersMois(intval($etudeManager->getDateFin($etude)->format('m')));
            $Date_Fin_Etude = $etudeManager->getDateFin($etude)->format('d/m/Y');
        }
        else
            $Mois_Fin = NULL;
        if ($etudeManager->getDelaiEtude($etude))
            $Delais_Semaines = (int) $etudeManager->getDelaiEtude($etude)->d / 7;
        else
            $Delais_Semaines = NULL;

        //Etude

        $Acompte_Pourcentage = (float) $etude->getPourcentageAcompte();
        $Acompte_HT = (float) $Montant_Total_Etude_HT * $Acompte_Pourcentage / 100;
        $Acompte_TTC = (float) $Montant_Total_Etude_TTC * $Acompte_Pourcentage / 100;
        $Acompte_TVA = (float) $Montant_Total_Etude_HT * ($Acompte_Pourcentage / 100) * $Taux_TVA / 100;
        $Solde_PVR_HT = (float) $Montant_Total_Etude_HT - $Acompte_HT;
        $Solde_PVR_TTC = (float) $Montant_Total_Etude_TTC - $Acompte_TTC;

        //Round
        $Part_TVA_Montant_Total_Etude = round($Part_TVA_Montant_Total_Etude, 2);
        $Acompte_HT = round($Acompte_HT, 2);
        $Acompte_TTC = round($Acompte_TTC, 2);
        $Acompte_TVA = round($Acompte_TVA, 2);
        $Solde_PVR_HT = round($Solde_PVR_HT, 2);
        $Solde_PVR_TTC = round($Solde_PVR_TTC, 2);

        //Conversion Lettre
        $Montant_Total_Etude_HT_Lettres = $converter->ConvNumberLetter($Montant_Total_Etude_HT, 1);
        $Montant_Total_Etude_TTC_Lettres = $converter->ConvNumberLetter($Montant_Total_Etude_TTC, 1);
        $Part_TVA_Montant_Total_Etude_Lettres = $converter->ConvNumberLetter($Part_TVA_Montant_Total_Etude, 1);
        $Montant_Total_JEH_HT_Lettres = $converter->ConvNumberLetter($Montant_Total_JEH_HT, 1);

        $Montant_Total_Frais_HT_Lettres = $converter->ConvNumberLetter($Montant_Total_Frais_HT, 1);
        $Acompte_HT_Lettres = $converter->ConvNumberLetter($Acompte_HT, 1);
        $Acompte_TTC_Lettres = $converter->ConvNumberLetter($Acompte_TTC, 1);
        $Acompte_TVA_Lettres = $converter->ConvNumberLetter($Acompte_TVA, 1);
        $Solde_PVR_HT_Lettres = $converter->ConvNumberLetter($Solde_PVR_HT, 1);
        $Solde_PVR_TTC_Lettres = $converter->ConvNumberLetter($Solde_PVR_TTC, 1);

        $champs = Array(
            'Presentation_Projet' => $etude->getPresentationProjet(),
            'Description_Prestation' => $etude->getDescriptionPrestation(),
            'Type_Prestation' => $etude->getTypePrestation(),
            'Capacites_Dev' => $etude->getCompetences(),
            'Nbr_JEH_Total' => $Nbr_JEH,
            'Nbr_JEH_Total_Lettres' => $Nbr_JEH_Lettres,
            'Montant_Total_JEH_HT' => $Montant_Total_JEH_HT,
            'Montant_Total_JEH_HT_Lettres' => $Montant_Total_JEH_HT_Lettres,
            'Montant_Total_Frais_HT' => $Montant_Total_Frais_HT,
            'Montant_Total_Frais_HT_Lettres' => $Montant_Total_Frais_HT_Lettres,
            'Montant_Total_Etude_HT' => $Montant_Total_Etude_HT,
            'Montant_Total_Etude_HT_Lettres' => $Montant_Total_Etude_HT_Lettres,
            'Montant_Total_Etude_TTC' => $Montant_Total_Etude_TTC,
            'Montant_Total_Etude_TTC_Lettres' => $Montant_Total_Etude_TTC_Lettres,
            'Taux_TVA' => $Taux_TVA,
            'Part_TVA_Montant_Total_Etude' => $Part_TVA_Montant_Total_Etude,
            'Part_TVA_Montant_Total_Etude_Lettres' => $Part_TVA_Montant_Total_Etude_Lettres,
            'Nbr_Phases' => $nombrePhase,
            'Mois_Lancement' => $Mois_Lancement,
            'Mois_Fin' => $Mois_Fin,
            'Delais_Semaines' => $Delais_Semaines,
            'Acompte_HT' => $Acompte_HT,
            'Acompte_HT_Lettres' => $Acompte_HT_Lettres,
            'Acompte_TTC' => $Acompte_TTC,
            'Acompte_TTC_Lettres' => $Acompte_TTC_Lettres,
            'Acompte_TVA' => $Acompte_TVA,
            'Acompte_TVA_Lettres' => $Acompte_TVA_Lettres,
            'Solde_PVR_HT' => $Solde_PVR_HT,
            'Solde_PVR_TTC' => $Solde_PVR_TTC,
            'Solde_PVR_HT_Lettres' => $Solde_PVR_HT_Lettres,
            'Solde_PVR_TTC_Lettres' => $Solde_PVR_TTC_Lettres,
            'Acompte_Pourcentage' => $Acompte_Pourcentage,
            'Date_Fin_Etude' => $Date_Fin_Etude,
        );

        //Doc
        if ($etude->getDoc($doc, $key) != NULL) {
            //Date Signature tout type de doc
            $dateSignature = $etude->getDoc($doc, $key)->getDateSignature();
            if ($dateSignature != NULL)
                $this->array_push_assoc($champs, 'Date_Signature', $dateSignature->format("d/m/Y"));

            //Signataire 1 : Signataire M-GaTE
            if ($etude->getDoc($doc)->getSignataire1() != NULL) {
                $signataire1 = $etude->getDoc($doc, $key)->getSignataire1();
                if ($signataire1 != NULL) {
                    $this->array_push_assoc($champs, 'Nom_Signataire_MGaTE', $signataire1->getPrenomNom());
                    $this->array_push_assoc($champs, 'Fonction_Signataire_MGaTE', $signataire1->getPoste());
                }
            }
            //Signataire 2 : Signataire Client
            if ($etude->getDoc($doc)->getSignataire2() != NULL) {//TODO remplacer par Signataire_Client
                $signataire2 = $etude->getDoc($doc, $key)->getSignataire2();
                if ($signataire1 != NULL) {
                    $this->array_push_assoc($champs, 'Nom_Signataire', $signataire2->getPrenomNom());
                    $this->array_push_assoc($champs, 'Fonction_Signataire', $signataire2->getPoste());
                }
            }
        }


        //Références
        $this->array_push_assoc($champs, 'Reference_Etude', $etudeManager->getRefEtude($etude));
        if ($etude->getAp())
            $this->array_push_assoc($champs, 'Reference_AP', $etudeManager->getRefDoc($etude, 'AP', $etude->getDoc('AP')->getVersion()));
        if ($etude->getCc())
            $this->array_push_assoc($champs, 'Reference_CC', $etudeManager->getRefDoc($etude, 'CC', $etude->getDoc('CC')->getVersion()));
        //if ($etude->getFactureAcompte())
        //    $this->array_push_assoc($champs, 'Reference_FA', $etudeManager->getRefDoc($etude, 'FA', $etude->get('FA')->getVersion()));
        if ($etude->getMissions())
            if ($etude->getDoc('RM', $key))
                $this->array_push_assoc($champs, 'Reference_RM', $etudeManager->getRefDoc($etude, 'RM', $etude->getDoc('RM', $key)->getVersion(), $key));

        //Prospect
        if ($etude->getProspect() != NULL) {
            $this->array_push_assoc($champs, 'Nom_Client', $etude->getProspect()->getNom());
            $this->array_push_assoc($champs, 'Entite_Sociale', $etude->getProspect()->getEntite());
            $this->array_push_assoc($champs, 'Adresse_Client', $etude->getProspect()->getAdresse());
        }

        //Suiveur
        if ($etude->getSuiveur() != NULL) {
            $this->array_push_assoc($champs, 'Mail_suiveur', $etude->getSuiveur()->getEmail());
            $this->array_push_assoc($champs, 'Nom_suiveur', $etude->getSuiveur()->getPrenomNom());

            if ($etude->getSuiveur()->getMobile() != NULL)
                $this->array_push_assoc($champs, 'Tel_suiveur', $etude->getSuiveur()->getMobile());
            else
                $this->array_push_assoc($champs, 'Tel_suiveur', $etude->getSuiveur()->getFix());
        }

        //Avant-Projet
        if ($etude->getAp() != NULL) {
            //Nombre dev
            $Nbr_Dev = $etude->getAp()->getNbrDev() + 0;
            $Nbre_Dev_Lettres = $converter->ConvNumberLetter($Nbr_Dev);
            $this->array_push_assoc($champs, 'Nbre_Developpeurs', $Nbr_Dev);
            $this->array_push_assoc($champs, 'Nbre_Developpeurs_Lettres', $Nbre_Dev_Lettres);
        }


        //Convention Client
        //Facture Acompte
        /* if ($etude->getFactureAcompte() != NULL) {
          $Date_Limite = clone $etude->getFactureAcompte()->getDateSignature();
          $Date_Limite->modify('+ 30 day');
          $this->array_push_assoc($champs, 'Date_Limite', $Date_Limite->format("d/m/Y"));
          } */


        //Phases
        foreach ($phases as $phase) {
            $i = $phase->getPosition() + 1;

            $this->array_push_assoc($champs, 'Phase_' . $i . '_Titre', $phase->getTitre());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Nbre_JEH', (int) $phase->getNbrJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_JEH', (float) $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_Phase_HT', (float) $phase->getNbrJEH() * $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_Phase', (float) $phase->getNbrJEH() * $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Date_Debut', $phase->getDateDebut()->format('d/m/Y'));
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Delai', $phase->getDelai());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Objectif', $phase->getObjectif());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Methodo', $phase->getMethodo());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Rendu', $phase->getValidation());
        }

        //Intervenant
        if ($mission = $etude->getMissions()->get($key)) {
            if ($mission->getIntervenant()->getPersonne()) {
                $sexe = ($mission->getIntervenant()->getPersonne()->getSexe() == 'M.' ? 1 : 2 );

                $this->array_push_assoc($champs, 'Nom_Etudiant', $mission->getIntervenant()->getPersonne()->getNom());
                $this->array_push_assoc($champs, 'Prenom_Etudiant', $mission->getIntervenant()->getPersonne()->getPrenom());
                $this->array_push_assoc($champs, 'Sexe_Etudiant', $sexe);
                $this->array_push_assoc($champs, 'Adresse_Etudiant', $mission->getIntervenant()->getPersonne()->getAdresse());
            }
            $Mission_Nbre_JEH = (int) 0;
            $Mission_Montant_JEH_Verse = (float) 0;
            foreach ($mission->getPhaseMission() as $phaseMission) {
                $Mission_Nbre_JEH += $phaseMission->getNbrJEH();
                $Mission_Montant_JEH_Verse += $phaseMission->getNbrJEH() * $phaseMission->getPhase()->getPrixJEH();
            }
            $Mission_Montant_JEH_Verse *= $mission->getPourcentageJunior() / 100;

            $Mission_Nbre_JEH_Lettres = $converter->ConvNumberLetter($Mission_Nbre_JEH);
            $Mission_Montant_JEH_Verse_Lettres = $converter->ConvNumberLetter($Mission_Montant_JEH_Verse, 1);

            $this->array_push_assoc($champs, 'Mission_Nbre_JEH', $Mission_Nbre_JEH);
            $this->array_push_assoc($champs, 'Mission_Nbre_JEH_Lettres', $Mission_Nbre_JEH_Lettres);
            $this->array_push_assoc($champs, 'Mission_Montant_JEH_Verse', $Mission_Montant_JEH_Verse);
            $this->array_push_assoc($champs, 'Mission_Montant_JEH_Verse_Lettres', $Mission_Montant_JEH_Verse_Lettres);
            $this->array_push_assoc($champs, 'Mission_Reference_CE', $etudeManager->getRefDoc($etude, "CE", $key));
        }



        //var_dump($champs);
        return $champs;
    }

    private function array_push_assoc(&$array, $key, $value) {
        $array[$key] = $value;
        return $array;
    }

    private function getEtudeFromID($id_etude) {
        $em = $this->getDoctrine()->getEntityManager();

        //Récupère l'étude avec son id
        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id_etude))
            throw $this->createNotFoundException('Etude[id=' . $id_etude . '] inexistant');

        return $etude;
    }

    private function getDoctypeAbsolutePathFromName($doc) {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->get('request');

        if (!$documenttype = $em->getRepository('mgate\PubliBundle\Entity\DocumentType')->findOneBy(array('name' => $doc))) {
            $chemin = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/bundles/mgatepubli/document-type/' . $doc . '.xml'; //asset
        } else {
            $chemin = $documenttype->getWebPath(); // on prend le document type qui est uploadé
        }
        return $chemin;
    }

    private function publipostage($id_etude, $doc, $key = 0) {
        $key = intval($key);

        $etude = $this->getEtudeFromID($id_etude);
        $chemin = $this->getDoctypeAbsolutePathFromName($doc);
        $nombrePhase = count($etude->getPhases());
        $champs = $this->getAllChamp($etude, $doc, $key);

        //DEBUG
        if ($this->container->getParameter('debugEnable')) {
            $path = $this->container->getParameter('pathToDoctype');
            $chemin = $path . $doc . '.xml';
        }



        $templateXMLtraite = $this->traiterTemplate($chemin, $nombrePhase, $champs);

        $champsBrut = $this->verifierTemplate($templateXMLtraite);

        $repertoire = 'tmp';

        if ($etude->getDoc($doc, $key))
            $refDocx = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc, $etude->getDoc($doc, $key)->getVersion(), $key);
        else
            $refDocx = 'ERROR';
        $idDocx = $refDocx . '-' . ((int) strtotime("now") + rand());


        if (!file_exists($repertoire))
            mkdir($repertoire/* ,0700 */);
        $handle = fopen($repertoire . '/' . $idDocx, "w+");
        fwrite($handle, $templateXMLtraite);
        fclose($handle);



        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $refDocx;


        return $champsBrut;
    }

    public function publiposterMultiple($id_etude, $doc) {
        $etude = $this->getEtudeFromID($id_etude);
        $refDocx = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc, $etude->getDoc($doc)->getVersion());
        $idZip = 'ZIP' . $refDocx . '-' . ((int) strtotime("now") + rand());
        $_SESSION['idZip'] = $idZip;


        $i = 0;
        foreach ($etude->getMissions() as $mission) {
            $this->publipostage($id_etude, $doc, $i);
            $this->telechargerAction('', true);
            $i++;
        }
        $this->telechargerAction('', false, true);
    }

    //publication du doc
    public function publiposterAction($id_etude, $doc, $key = -1) {

        if ($doc == 'RM' && $key == -1)
            $champsBrut = $this->publiposterMultiple($id_etude, $doc);
        else
            $champsBrut = $this->publipostage($id_etude, $doc, $key);


        if (count($champsBrut)) {
            return $this->render('mgatePubliBundle:Traitement:index.html.twig', array('nbreChampsNonRemplis' => count($champsBrut), 'champsNonRemplis' => $champsBrut,));
        } else {
            return $this->telechargerAction($doc);
        }
    }

    //A nettoyer !!!
    public function telechargerAction($docType = 'AP', $addZip = false, $dlZip = false) {
        $this->purge();
        //TODO idDocx.$doc
        if (isset($_SESSION['idDocx']) && isset($_SESSION['refDocx'])) {
            $idDocx = $_SESSION['idDocx'];
            $refDocx = $_SESSION['refDocx'];


            if ($addZip) {
                $idZip = $_SESSION['idZip'];
                $zip = new \ZipArchive;
                $zip->open('tmp/' . $idZip, \ZipArchive::CREATE);
                $zip->addFile('tmp/' . $idDocx, $refDocx . '.xml');
                $zip->close();
            } elseif ($dlZip) {
                $idZip = $_SESSION['idZip'];
                $doc = 'tmp/' . $idZip;

                header('Content-Type: application/zip');
                header('Content-Length: ' . filesize($doc));
                header('Content-disposition: inline; filename=' . $refDocx . '.zip');
                header('Pragma: no-cache');
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');
                readfile($doc);
                exit();
            } else {
                $doc = 'tmp/' . $idDocx;

                header('Content-Type: application/msword');
                header('Content-Length: ' . filesize($doc));
                header('Content-disposition: attachment; filename=' . $refDocx);
                header('Pragma: no-cache');
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');
                readfile($doc);
                exit();
            }
        } else {
            echo 'fail';
        }

        return $this->redirect($this->generateUrl('mgateSuivi_etude_homepage', array('page' => 1)));
    }

    //Nettoie le dossier tmp : efface les fichiers temporaires vieux de plus de n = 1 jours
    private function purge() {
        $Patern = '*';
        $oldSec = 86400; // = 1 Jours
        $path = 'tmp/';
        clearstatcache();
        foreach (@glob($path . $Patern) as $filename) {
            if (filemtime($filename) + $oldSec < time())
                @unlink($filename);
        }
    }

    //Formate un nombre à la francaise ex 1 234,56 avec deux décimals 
    //SEE_ALSO moneyformat LC_MONETARY fr_FR
    private function formaterNombre($number) {
        if (is_int($number)) // Si entier
            return number_format($number, 0, ',', ' ');
        else
            return number_format($number, 2, ',', ' ');
    }

}
