<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

class TraitementController extends Controller {

    private $SFD = '~';                                                         //Start Field       Delimiter
    private $EFD = '~~';                                                        //End   Field       Delimiter
    private $STRD = '<!--repeatTR-->';                                          //Start TableRow    Delimiter   
    private $ETRD = '<!--/repeatTR-->';                                         //End   TableRow    Delimiter
    private $SPD = '<!--repeatP-->';                                            //Start Paragraph   Delimiter
    private $EPD = '<!--/repeatP-->';                                           //End   Paragraph   Delimiter
    private $SGD = '¤';                                                         //Start Grammar     Delimiter
    private $EGD = '¤¤';                                                        //End   Grammar     Delimiter
    private $SLD = 'µ';                                                         //Start Liaison     Delimiter
    private $ELD = 'µµ';                                                        //End   Liaison     Delimiter

    private function repeatTR(&$templateXML, $nombreRepeat) {

        $regexRepeatSTART = $this->STRD;
        $regexRepeatEND = $this->ETRD;
        $regexpRepeat = '#' . $regexRepeatSTART . '(.*?)' . $regexRepeatEND . '#s'; // *? see ungreedy behavior //Expression régulière filtrage répétition /!\ imbrication interdite !

        $SFD = $this->SFD;
        $EFD = $this->EFD;
        $callback = function ($matches) use ($nombreRepeat, $SFD, $EFD) { //Fonction de callback prétraitement de la zone à répéter
                    $outputString = "";

                    /* Selection du nombre de répétition :
                     * $nombreRepeat[0] = nombrePhase
                     * $nombreRepeat[1] = nombreDev
                     */
                    if (preg_match("#{{DEV}}#", $matches[1]))
                        $repetition = $nombreRepeat[1];
                    else
                        $repetition = $nombreRepeat[0];
                    $matches[1] = preg_replace('#{{\w+}}#', '', $matches[1]);
                    //

                    if (preg_match("#w:vMerge\s*/>#", $matches[1]))//Vérification de rowspan
                        $premiereLigne = preg_replace('#<w:vMerge\s*/>#', "<w:vMerge w:val=\"restart\"/>", $matches[1]);
                    else
                        $premiereLigne = $matches[1];

                    $outputString .= preg_replace('#' . $SFD . 'Index' . $EFD . '#U', "1", $premiereLigne);

                    for ($i = 2; $i <= $repetition; $i++)
                        $outputString .= preg_replace('#' . $SFD . 'Index' . $EFD . '#U', "$i", $matches[1]);
                    return $outputString;
                };

        $templateXML = preg_replace_callback($regexpRepeat, $callback, $templateXML);

        return $templateXML;
    }

    private function repeatP(&$templateXML, $nombreRepeat) {
        $regexRepeatSTART = $this->SPD; //Marqueur de début de repeat
        $regexRepeatEND = $this->EPD; //Marqueur de fin de repeat
        $regexpRepeat = '#' . $regexRepeatSTART . '(.*?)' . $regexRepeatEND . '#s'; // *? see ungreedy behavior //Expression régulière filtrage répétition /!\ imbrication interdite !

        $SFD = $this->SFD;
        $EFD = $this->EFD;
        $callback = function ($matches) use ($nombreRepeat, $SFD, $EFD) { //Fonction de callback prétraitement de la zone à répéter
                    $outputString = "";

                    /* Selection du nombre de répétition :
                     * $nombreRepeat[0] = nombrePhase
                     * $nombreRepeat[1] = nombreDev
                     */
                    if (preg_match("#{{DEV}}#", $matches[1]))
                        $repetition = $nombreRepeat[1];
                    else
                        $repetition = $nombreRepeat[0];
                    $matches[1] = preg_replace('#{{\w+}}#', '', $matches[1]);
                    //


                    for ($i = 1; $i <= $repetition; $i++)
                        $outputString .= preg_replace('#' . $SFD . 'Index' . $EFD . '#U', "$i", $matches[1]);
                    return $outputString;
                };

        $templateXML = preg_replace_callback($regexpRepeat, $callback, $templateXML);

        return $templateXML;
    }

    //Repétition des phases
    private function repeterBlock(&$templateXML, $nombreRepeat) {
        $this->repeatTR($templateXML, $nombreRepeat);
        $this->repeatP($templateXML, $nombreRepeat);
        return $templateXML;
    }

    //Remplissage des %champs%
    private function remplirChamps(&$templateXML, $fieldValues) {
        $SFD = $this->SFD;

        $EFD = $this->EFD;

        foreach ($fieldValues as $field => $values) {//Remplacement des champs
            //WARNING : ($values != NULL) remplacer par ($values !== NULL), les valeurs NULL de type non NULL sont permises !!!!!!!
            //TODO : Verification type NULL sur champs vide 
            if ($values !== NULL) {
                if (is_int($values) || is_float($values)) //Formatage des nombres à la francaise
                    $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', preg_replace("# #", " ", $this->formaterNombre($values)), $templateXML);
                else
                    $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', $this->nl2wbr(htmlspecialchars($values)), $templateXML);
            }
        }


        return $templateXML;
    }

    //Converti les retours à la ligne en retour à la ligne pour word
    private function nl2wbr($input) {
        return preg_replace('#(\\r\\n)|(\\n)|(\\r)#', '<w:br />', $input);
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

    //Vérification du fichier
    //if match % _ % then pasbien
    private function verifierTemplates($templatesXML) {
        $SFD = $this->SFD;
        $EFD = $this->EFD;
        $allmatches = array();

        foreach ($templatesXML as $templateXML) {
            preg_match_all('#' . $SFD . '(.*?)' . $EFD . '#', $templateXML, $matches);
            $allmatches += $matches[1];
        }

        return $allmatches;
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

        if ($etudeManager->getDateLancement($etude)) {
            $Mois_Lancement = $this->nombreVersMois(intval($etudeManager->getDateLancement($etude)->format('m')));
            $Date_Debut_Etude = $etudeManager->getDateLancement($etude)->format("d/m/Y");
        } else {
            $Mois_Lancement = NULL;
            $Date_Debut_Etude = NULL;
        }

        if ($etudeManager->getDateFin($etude)) {
            $Mois_Fin = $this->nombreVersMois(intval($etudeManager->getDateFin($etude)->format('m')));
            $Date_Fin_Etude = $etudeManager->getDateFin($etude)->format('d/m/Y');
        } else {
            $Mois_Fin = NULL;
            $Date_Fin_Etude = NULL;
        }
        if ($etudeManager->getDelaiEtude($etude)) {
            $Delais_Semaines = $this->jourVersSemaine(((int) $etudeManager->getDelaiEtude($etude)->days));
        }
        else
            $Delais_Semaines = NULL;



        //Etude

        $Acompte_Pourcentage = (float) $etude->getPourcentageAcompte();
        $Acompte_HT = (float) $Montant_Total_Etude_HT * $Acompte_Pourcentage;
        $Acompte_TTC = (float) $Montant_Total_Etude_TTC * $Acompte_Pourcentage;
        $Acompte_TVA = (float) $Montant_Total_Etude_HT * ($Acompte_Pourcentage) * $Taux_TVA / 100;
        $Solde_PVR_HT = (float) $Montant_Total_Etude_HT - $Acompte_HT;
        $Solde_PVR_TTC = (float) $Montant_Total_Etude_TTC - $Acompte_TTC;
        $Acompte_Pourcentage *= 100;

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
            'Type_Prestation' => $etude->getTypePrestationToString(),
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
            'Date_Debut_Etude' => $Date_Debut_Etude,
            'Date_Fin_Etude' => $Date_Fin_Etude,
        );

        //Doc
        if ($etude->getDoc($doc, $key) != NULL) {
            //Date Signature tout type de doc
            $dateSignature = $etude->getDoc($doc, $key)->getDateSignature();
            if ($dateSignature)
                $this->array_push_assoc($champs, 'Date_Signature', $dateSignature->format("d/m/Y"));

            //Signataire 1 : Signataire M-GaTE
            if ($etude->getDoc($doc, $key)->getSignataire1() != NULL) {
                $signataire1 = $etude->getDoc($doc, $key)->getSignataire1();
                if ($signataire1 != NULL) {
                    $this->array_push_assoc($champs, 'Nom_Signataire_Mgate', $signataire1->getNomFormel());
                    $this->array_push_assoc($champs, 'Fonction_Signataire_Mgate', mb_strtolower($signataire1->getPoste(), 'UTF-8'));
                    $this->array_push_assoc($champs, 'Sexe_Signataire_Mgate', ($signataire1->getSexe() == 'M.' ? 1 : 2));
                }
            }
            //Signataire 2 : Signataire Client
            if ($etude->getDoc($doc, $key)->getSignataire2() != NULL) {
                $signataire2 = $etude->getDoc($doc, $key)->getSignataire2();
                if ($signataire2 != NULL) {
                    $this->array_push_assoc($champs, 'Nom_Signataire_Client', $signataire2->getNomFormel());
                    $this->array_push_assoc($champs, 'Fonction_Signataire_Client', mb_strtolower($signataire2->getPoste(), 'UTF-8'));
                }
            }
        }









        //Prospect
        if ($etude->getProspect() != NULL) {
            $this->array_push_assoc($champs, 'Nom_Client', $etude->getProspect()->getNom());
            if ($etude->getProspect()->getEntite())
                $this->array_push_assoc($champs, 'Entite_Sociale', $etude->getProspect()->getEntiteToString());
            else
                $this->array_push_assoc($champs, 'Entite_Sociale', ' ');
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

            if ($etude->getAp()->getContactMgate()) {
                $this->array_push_assoc($champs, 'Nom_Contact_Mgate', $etude->getAp()->getContactMgate()->getNom());
                $this->array_push_assoc($champs, 'Prenom_Contact_Mgate', $etude->getAp()->getContactMgate()->getPrenom());
                $this->array_push_assoc($champs, 'Mail_Contact_Mgate', $etude->getAp()->getContactMgate()->getEmail());
                $this->array_push_assoc($champs, 'Tel_Contact_Mgate', $etude->getAp()->getContactMgate()->getMobile());
                $this->array_push_assoc($champs, 'Fonction_Contact_Mgate', mb_strtolower($etude->getAp()->getContactMgate()->getPoste(), 'UTF-8'));
            }
        }


        //Convention Client
        //Facture Acompte
        if ($etude->getFa()) {
            if ($etude->getFa()->getDateSignature()) {
                $Date_Limite = clone $etude->getFa()->getDateSignature();
                $Date_Limite->modify('+ 30 day');
                $this->array_push_assoc($champs, 'Date_Limite', $Date_Limite->format("d/m/Y"));
            }
        }
        //Facture de solde
        if ($etude->getFs()) {
            if ($etude->getFs()->getDateSignature()) {
                $Date_Limite = clone $etude->getFs()->getDateSignature();
                $Date_Limite->modify('+ 30 day');
                $this->array_push_assoc($champs, 'Date_Limite', $Date_Limite->format("d/m/Y"));
            }

            $Reste_HT = $etude->getFs()->getMontantHT();
            $Reste_TTC = (float) round($Reste_HT * (1 + $Taux_TVA / 100), 2);
            $Reste_TTC_Lettres = $converter->ConvNumberLetter($Reste_TTC, 1);
            $Reste_TVA = (float) round($Reste_HT * $Taux_TVA / 100, 2);
            $this->array_push_assoc($champs, 'Reste_HT', $Reste_HT);
            $this->array_push_assoc($champs, 'Reste_TTC', $Reste_TTC);
            $this->array_push_assoc($champs, 'Reste_TTC_Lettres', $Reste_TTC_Lettres);
            $this->array_push_assoc($champs, 'Reste_TVA', $Reste_TVA);
        }

        //Factures de solde et intermediaires
        $Solde_Intermediaire_HT = (float) 0;
        $Factures_Intermediaires_HT = (float) 0;
        $i = 0;
        foreach ($etude->getFis() as $fi) {
            if ($doc != 'FI' || $i < $key)
                $Factures_Intermediaires_HT += $fi->getMontantHT();
            else if ($i == $key)
                $Solde_Intermediaire_HT = (float) $fi->getMontantHT();
            $i++;
        }

        $Deja_Paye_HT = (float) ($Acompte_HT + $Factures_Intermediaires_HT);
        $Part_TVA_Deja_Paye = (float) $Deja_Paye_HT * $Taux_TVA / 100;
        $Deja_Paye_TTC = (float) $Deja_Paye_HT * (1 + $Taux_TVA / 100);
        $Part_TVA_Solde_Intermediaire = (float) $Solde_Intermediaire_HT * $Taux_TVA / 100;
        $Solde_Intermediaire_TTC = (float) $Solde_Intermediaire_HT * (1 + $Taux_TVA / 100);
        $Solde_Intermediaire_TTC_Lettres = $converter->ConvNumberLetter($Solde_Intermediaire_TTC, 1);

        $this->array_push_assoc($champs, 'Solde_Intermediaire_TTC_Lettres', $Solde_Intermediaire_TTC_Lettres);
        $this->array_push_assoc($champs, 'Solde_Intermediaire_TTC', $Solde_Intermediaire_TTC);
        $this->array_push_assoc($champs, 'Solde_Intermediaire_HT', $Solde_Intermediaire_HT);
        $this->array_push_assoc($champs, 'Part_TVA_Solde_Intermediaire', $Part_TVA_Solde_Intermediaire);
        $this->array_push_assoc($champs, 'Factures_Intermediaires_HT', $Factures_Intermediaires_HT);
        $this->array_push_assoc($champs, 'Deja_Paye_HT', $Deja_Paye_HT);
        $this->array_push_assoc($champs, 'Deja_Paye_TTC', $Deja_Paye_TTC);
        $this->array_push_assoc($champs, 'Part_TVA_Deja_Paye', $Part_TVA_Deja_Paye);

        //PREPARE PVI
        $nbrPVI = count($etude->getPvis());
        if ($doc == 'PVI' && $key < $nbrPVI)
            $phasePVI = $etude->getPvis($key)->getPhaseID();
        else
            $phasePVI = -1;

        //PVR
        if ($etude->getAvs())
            $Nbr_Avenant = count($etude->getAvs()->getValues());
        else
            $Nbr_Avenant = 0;
        $this->array_push_assoc($champs, 'Nbr_Avenant', $Nbr_Avenant + 1);

        //PVI
        if ($doc == 'PVI') {
            if ($key < count($etude->getPvis()))
                $this->array_push_assoc($champs, 'Phase_PVI', $phasePVI);
        }

        //Références
        $this->array_push_assoc($champs, 'Reference_Etude', $etudeManager->getRefEtude($etude));
        foreach (array('AP', 'CC', 'FA', 'PVR', 'FS', 'PVI', 'RM', 'DM', 'FI') as $abrv) {
            if ($etude->getDoc($abrv, $key) || $abrv == 'DM')
                $this->array_push_assoc($champs, 'Reference_' . $abrv, $etudeManager->getRefDoc($etude, $abrv, $key));
        }
        if ($etude->getDoc('AV', $Nbr_Avenant - 1)) {//key of AV1 = 0
            if ($etude->getDoc('CC'))
                $this->array_push_assoc($champs, 'Reference_AVCC', $etudeManager->getRefDoc($etude, 'AVCC', $Nbr_Avenant - 1));
        }

        if ($etude->getDoc('RM', $key)) {
            $this->array_push_assoc($champs, 'Mission_Reference_CE', $etudeManager->getRefDoc($etude, 'CE', $key));
        }

        //Phases
        foreach ($phases as $phase) {
            $i = $phase->getPosition() + 1;

            $validation = $phase->getValidationChoice();

            $this->array_push_assoc($champs, 'Phase_' . $i . '_Titre', $phase->getTitre());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Nbre_JEH', (int) $phase->getNbrJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_JEH', (float) $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_Phase_HT', (float) $phase->getNbrJEH() * $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_Phase', (float) $phase->getNbrJEH() * $phase->getPrixJEH());
            if ($phase->getDateDebut())
                $this->array_push_assoc($champs, 'Phase_' . $i . '_Date_Debut', $phase->getDateDebut()->format('d/m/Y'));
            if ($phase->getDateDebut()) {
                $dateFin = clone $phase->getDateDebut(); //WARN $a = $b : $a pointe vers le même objet que $b...
                $dateFin->modify('+' . $phase->getDelai() . ' day');
                $this->array_push_assoc($champs, 'Phase_' . $i . '_Date_Fin', $dateFin->format('d/m/Y'));
            }
            $Delai = $this->jourVersSemaine($phase->getDelai());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Delai', $Delai); //délai en semaine
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Objectif', $phase->getObjectif());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Methodo', $phase->getMethodo());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Rendu', $validation[$phase->getValidation()]);



            //PVI
            if ($doc == 'PVI' && $i == $phasePVI) {
                $this->array_push_assoc($champs, 'Phase_PVI_Objectif', $phase->getObjectif());
            }
        }

        //DM : Autres dev
        $i = 1;
        $phaseDev = array();
        foreach ($etude->getMissions() as $mission) {

            /**
             * @todo Issue #21
             */
            if ($i == $key + 1) { // Phase concernant l'intervenant


                /* $phaseDev = '';
                  foreach ($mission->getPhaseMission()->getValues() as $phaseMission) {
                  if ($phaseMission->getNbrJEH())
                  $phaseDev[$phaseMission->getPhase()->getPosition() + 1] = $phaseMission->getPhase()->getTitre();
                  }
                 */

                //Referent Technique
                if ($mission) {
                    if ($refTechnique = $mission->getReferentTechnique()) {
                        $this->array_push_assoc($champs, 'Prenom_Referent_Technique', $refTechnique->getPersonne()->getPrenom());
                        $this->array_push_assoc($champs, 'Nom_Referent_Technique', $refTechnique->getPersonne()->getNom());
                        $this->array_push_assoc($champs, 'Mail_Referent_Technique', $refTechnique->getPersonne()->getEmail());
                        $this->array_push_assoc($champs, 'Tel_Referent_Technique', $refTechnique->getPersonne()->getMobile());
                    }
                }
            }
            if ($mission) { // Autre intervenants
                if ($intervenant = $mission->getIntervenant())
                    if ($intervenant = $intervenant->getPersonne()) {
                        $this->array_push_assoc($champs, 'Developpeur_' . $i . '_Nom', $intervenant->getNomFormel());
                        $this->array_push_assoc($champs, 'Developpeur_' . $i . '_Mail', $intervenant->getEmail());
                        $this->array_push_assoc($champs, 'Developpeur_' . $i . '_Tel', $intervenant->getMobile());
                    }
            }
            $i++;
        }
        array_multisort($phaseDev);
        $phaseDevString = "";
        foreach ($phaseDev as $keys => $value)
            $phaseDevString .= ($keys) . ' - ' . $value . '\r\n';
        $this->array_push_assoc($champs, 'Phase_Dev', $phaseDevString);

        //Intervenant
        $mission = $etude->getMissions()->get($key);
        if ($mission) {
            if ($mission->getIntervenant()->getPersonne()) {
                $sexe = ($mission->getIntervenant()->getPersonne()->getSexe() == 'M.' ? 1 : 2 );

                $this->array_push_assoc($champs, 'Nom_Etudiant', $mission->getIntervenant()->getPersonne()->getNom());
                $this->array_push_assoc($champs, 'Prenom_Etudiant', $mission->getIntervenant()->getPersonne()->getPrenom());
                $this->array_push_assoc($champs, 'Sexe_Etudiant', $sexe);
                $this->array_push_assoc($champs, 'Adresse_Etudiant', $mission->getIntervenant()->getPersonne()->getAdresse());
                $this->array_push_assoc($champs, 'Nom_Formel_Etudiant', $mission->getIntervenant()->getPersonne()->getNomFormel());
            }

            $Mission_Remuneration = $mission->getRemuneration();
            $Mission_Nbre_JEH = (int) $Mission_Remuneration['jehRemuneration'];
            $Mission_Montant_JEH_Verse = (float) $Mission_Remuneration['montantRemuneration'];

            $Mission_Nbre_JEH_Lettres = $converter->ConvNumberLetter($Mission_Nbre_JEH);
            $Mission_Montant_JEH_Verse_Lettres = $converter->ConvNumberLetter($Mission_Montant_JEH_Verse, 1);

            $this->array_push_assoc($champs, 'Mission_Nbre_JEH', $Mission_Nbre_JEH);
            $this->array_push_assoc($champs, 'Mission_Nbre_JEH_Lettres', $Mission_Nbre_JEH_Lettres);
            $this->array_push_assoc($champs, 'Mission_Montant_JEH_Verse', $Mission_Montant_JEH_Verse);
            $this->array_push_assoc($champs, 'Mission_Montant_JEH_Verse_Lettres', $Mission_Montant_JEH_Verse_Lettres);

            if ($mission->getFinOm())
                $this->array_push_assoc($champs, 'Date_Fin_Mission', $mission->getFinOm()->format("d/m/Y"));
        }
        return $champs;
    }

    private function getDocRedigerRoute($doc, $key = 0) {
        $route = array(
            'AP' => 'mgateSuivi_ap_rediger',
            'CC' => 'mgateSuivi_cc_rediger',
            'FA' => 'mgateSuivi_facture_voir',
            'FI' => 'mgateSuivi_facture_voir',
            'FS' => 'mgateSuivi_facture_voir',
            'PVI' => 'mgateSuivi_procesverbal_voir',
            'PVR' => 'mgateSuivi_procesverbal_voir',
            'RM' => 'mgateSuivi_missions_modifier',
            'DM' => 'mgateSuivi_missions_modifier',
        );



        return $route[$doc];
    }

    private function getAidesEtude($etude, $doc, $key = 0) {
        $router = $this->get('router');


        $phases = $etude->getPhases();
        $nombrePhase = (int) count($phases);

        // $router->generate('mgateSuivi_ap_rediger', array(), true)

        $aides = Array(
            'Presentation_Projet' => $router->generate('mgateSuivi_ap_rediger', array('id' => $etude->getId()), true),
            'Description_Prestation' => $router->generate('mgateSuivi_ap_rediger', array('id' => $etude->getId()), true),
            'Type_Prestation' => $router->generate('mgateSuivi_ap_rediger', array('id' => $etude->getId()), true),
            'Capacites_Dev' => $router->generate('mgateSuivi_ap_rediger', array('id' => $etude->getId()), true),
            'Nbr_JEH_Total' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Nbr_JEH_Total_Lettres' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_JEH_HT' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_JEH_HT_Lettres' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_Frais_HT' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_Frais_HT_Lettres' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_Etude_HT' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_Etude_HT_Lettres' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_Etude_TTC' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Montant_Total_Etude_TTC_Lettres' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Part_TVA_Montant_Total_Etude' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Part_TVA_Montant_Total_Etude_Lettres' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Nbr_Phases' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Mois_Lancement' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Mois_Fin' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Delais_Semaines' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Acompte_HT' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Acompte_HT_Lettres' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Acompte_TTC' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Acompte_TTC_Lettres' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Acompte_TVA' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Acompte_TVA_Lettres' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Solde_PVR_HT' => '',
            'Solde_PVR_TTC' => '',
            'Solde_PVR_HT_Lettres' => '',
            'Solde_PVR_TTC_Lettres' => '',
            'Acompte_Pourcentage' => $router->generate('mgateSuivi_cc_rediger', array('id' => $etude->getId()), true),
            'Date_Debut_Etude' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
            'Date_Fin_Etude' => $router->generate('mgateSuivi_phases_modifier', array('id' => $etude->getId()), true),
        );

        $this->array_push_assoc($aides, 'Date_Signature', $router->generate($this->getDocRedigerRoute($doc, $key), array('id' => $etude->getId()), true));
        $this->array_push_assoc($aides, 'Nom_Signataire_Mgate', $router->generate($this->getDocRedigerRoute($doc, $key), array('id' => $etude->getId()), true));
        $this->array_push_assoc($aides, 'Nom_Signataire_Client', $router->generate($this->getDocRedigerRoute($doc, $key), array('id' => $etude->getId()), true));
        if ($etude->getDoc($doc, $key) != NULL) {
            //Signataire 1 : Signataire M-GaTE
            if ($etude->getDoc($doc, $key)->getSignataire1() != NULL) {
                $signataire1 = $etude->getDoc($doc, $key)->getSignataire1();
                if ($signataire1 = $signataire1->getMembre()) {
                    $this->array_push_assoc($aides, 'Fonction_Signataire_Mgate', $router->generate('mgatePersonne_membre_voir', array('id' => $signataire1->getId()), true));
                    $this->array_push_assoc($aides, 'Sexe_Signataire_Mgate', $router->generate('mgatePersonne_membre_voir', array('id' => $signataire1->getId()), true));
                }
            }
            //Signataire 2 : Signataire Client
            if ($etude->getDoc($doc, $key)->getSignataire2() != NULL) {
                $signataire2 = $etude->getDoc($doc, $key)->getSignataire2();
                if ($signataire2 = $signataire2->getEmploye()) {
                    $this->array_push_assoc($aides, 'Fonction_Signataire_Client', $router->generate('mgatePersonne_employe_voir', array('id' => $signataire2->getId()), true));
                }
            }
        }

        //Prospect
        $this->array_push_assoc($aides, 'Nom_Client', $router->generate($this->getDocRedigerRoute($doc, $key), array('id' => $etude->getId()), true));
        if ($etude->getProspect() != NULL) {
            $this->array_push_assoc($aides, 'Entite_Sociale', $router->generate('mgatePersonne_prospect_voir', array('id' => $etude->getProspect()->getId()), true));
            $this->array_push_assoc($aides, 'Adresse_Client', $router->generate('mgatePersonne_prospect_voir', array('id' => $etude->getProspect()->getId()), true));
        }


        //Suiveur
        $this->array_push_assoc($aides, 'Nom_suiveur', $router->generate($this->getDocRedigerRoute($doc, $key), array('id' => $etude->getId()), true));
        if ($etude->getSuiveur() != NULL && $suiveur = $etude->getSuiveur()->getMembre()) {
            $this->array_push_assoc($aides, 'Mail_suiveur', $router->generate('mgatePersonne_membre_voir', array('id' => $suiveur->getId()), true));
            $this->array_push_assoc($aides, 'Tel_suiveur', $router->generate('mgatePersonne_membre_voir', array('id' => $suiveur->getId()), true));
        }


        //Avant-Projet
        if ($etude->getAp() != NULL) {
            $this->array_push_assoc($aides, 'Nbre_Developpeurs', $router->generate($this->getDocRedigerRoute('AP'), array('id' => $etude->getId()), true));
            $this->array_push_assoc($aides, 'Nbre_Developpeurs_Lettres', $router->generate($this->getDocRedigerRoute('AP'), array('id' => $etude->getId()), true));
            $this->array_push_assoc($aides, 'Nom_Contact_Mgate', $router->generate($this->getDocRedigerRoute('AP'), array('id' => $etude->getId()), true));
            if ($etude->getAp()->getContactMgate() && $etude->getAp()->getContactMgate()->getMembre() && $etude->getAp()->getContactMgate()->getMembre()->getId()) {
                $contact = $etude->getAp()->getContactMgate()->getMembre()->getId();
                $this->array_push_assoc($aides, 'Prenom_Contact_Mgate', $router->generate('mgatePersonne_membre_voir', array('id' => $contact), true));
                $this->array_push_assoc($aides, 'Mail_Contact_Mgate', $router->generate('mgatePersonne_membre_voir', array('id' => $contact), true));
                $this->array_push_assoc($aides, 'Tel_Contact_Mgate', $router->generate('mgatePersonne_membre_voir', array('id' => $contact), true));
                $this->array_push_assoc($aides, 'Fonction_Contact_Mgate', $router->generate('mgatePersonne_membre_voir', array('id' => $contact), true));
            }
        }

         /*//Convention Client
          //Facture Acompte
          if ($etude->getFa()) {
          if ($etude->getFa()->getDateSignature()) {
          $Date_Limite = clone $etude->getFa()->getDateSignature();
          $Date_Limite->modify('+ 30 day');
          $this->array_push_assoc($aides, 'Date_Limite', $Date_Limite->format("d/m/Y"));
          }
          }
          //Facture de solde
          if ($etude->getFs()) {
          if ($etude->getFs()->getDateSignature()) {
          $Date_Limite = clone $etude->getFs()->getDateSignature();
          $Date_Limite->modify('+ 30 day');
          $this->array_push_assoc($aides, 'Date_Limite', $Date_Limite->format("d/m/Y"));
          }

          $Reste_HT = $etude->getFs()->getMontantHT();
          $Reste_TTC = (float) round($Reste_HT * (1 + $Taux_TVA / 100), 2);
          $Reste_TTC_Lettres = $converter->ConvNumberLetter($Reste_TTC, 1);
          $Reste_TVA = (float) round($Reste_HT * $Taux_TVA / 100, 2);
          $this->array_push_assoc($aides, 'Reste_HT', $Reste_HT);
          $this->array_push_assoc($aides, 'Reste_TTC', $Reste_TTC);
          $this->array_push_assoc($aides, 'Reste_TTC_Lettres', $Reste_TTC_Lettres);
          $this->array_push_assoc($aides, 'Reste_TVA', $Reste_TVA);
          }

          //Factures de solde et intermediaires
          $Solde_Intermediaire_HT = (float) 0;
          $Factures_Intermediaires_HT = (float) 0;
          $i = 0;
          foreach ($etude->getFis() as $fi) {
          if ($doc != 'FI' || $i < $key)
          $Factures_Intermediaires_HT += $fi->getMontantHT();
          else if ($i == $key)
          $Solde_Intermediaire_HT = (float) $fi->getMontantHT();
          //WARN INCORECTE LE NUM LES FI NE SONT PAS TOUJOURS DANS L'ORDRE EN BDD IDEM POUR GET REF DOC
          //IL FAUT AJOUTER UN CHAMP NUM DE FI
          $i++;
          }

          $Deja_Paye_HT = (float) ($Acompte_HT + $Factures_Intermediaires_HT);
          $Part_TVA_Deja_Paye = (float) $Deja_Paye_HT * $Taux_TVA / 100;
          $Deja_Paye_TTC = (float) $Deja_Paye_HT * (1 + $Taux_TVA / 100);
          $Part_TVA_Solde_Intermediaire = (float) $Solde_Intermediaire_HT * $Taux_TVA / 100;
          $Solde_Intermediaire_TTC = (float) $Solde_Intermediaire_HT * (1 + $Taux_TVA / 100);
          $Solde_Intermediaire_TTC_Lettres = $converter->ConvNumberLetter($Solde_Intermediaire_TTC, 1);

          $this->array_push_assoc($aides, 'Solde_Intermediaire_TTC_Lettres', $Solde_Intermediaire_TTC_Lettres);
          $this->array_push_assoc($aides, 'Solde_Intermediaire_TTC', $Solde_Intermediaire_TTC);
          $this->array_push_assoc($aides, 'Solde_Intermediaire_HT', $Solde_Intermediaire_HT);
          $this->array_push_assoc($aides, 'Part_TVA_Solde_Intermediaire', $Part_TVA_Solde_Intermediaire);
          $this->array_push_assoc($aides, 'Factures_Intermediaires_HT', $Factures_Intermediaires_HT);
          $this->array_push_assoc($aides, 'Deja_Paye_HT', $Deja_Paye_HT);
          $this->array_push_assoc($aides, 'Deja_Paye_TTC', $Deja_Paye_TTC);
          $this->array_push_assoc($aides, 'Part_TVA_Deja_Paye', $Part_TVA_Deja_Paye);

          //PREPARE PVI
          $nbrPVI = count($etude->getPvis());
          if ($doc == 'PVI' && $key < $nbrPVI)
          $phasePVI = $etude->getPvis($key)->getPhaseID();
          else
          $phasePVI = -1;

          //PVR
          if ($etude->getAvs())
          $Nbr_Avenant = count($etude->getAvs()->getValues());
          else
          $Nbr_Avenant = 0;
          $this->array_push_assoc($aides, 'Nbr_Avenant', $Nbr_Avenant + 1);

          //PVI
          if ($doc == 'PVI') {
          if ($key < count($etude->getPvis()))
          $this->array_push_assoc($aides, 'Phase_PVI', $phasePVI);
          }

          //Références
          $this->array_push_assoc($aides, 'Reference_Etude', $etudeManager->getRefEtude($etude));
          foreach (array('AP', 'CC', 'FA', 'PVR', 'FS', 'PVI', 'RM', 'DM', 'FI') as $abrv) {
          if ($etude->getDoc($abrv, $key) || $abrv == 'DM')
          $this->array_push_assoc($aides, 'Reference_' . $abrv, $etudeManager->getRefDoc($etude, $abrv, $key));
          }
          if ($etude->getDoc('AV', $Nbr_Avenant - 1)) {//key of AV1 = 0
          if ($etude->getDoc('CC'))
          $this->array_push_assoc($aides, 'Reference_AVCC', $etudeManager->getRefDoc($etude, 'AVCC', $Nbr_Avenant - 1));
          }

          if ($etude->getDoc('RM', $key)) {
          $this->array_push_assoc($aides, 'Mission_Reference_CE', $etudeManager->getRefDoc($etude, 'CE', $key));
          }

          //Phases
          foreach ($phases as $phase) {
          $i = $phase->getPosition() + 1;

          $validation = $phase->getValidationChoice();

          $this->array_push_assoc($aides, 'Phase_' . $i . '_Titre', $phase->getTitre());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Nbre_JEH', (int) $phase->getNbrJEH());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Prix_JEH', (float) $phase->getPrixJEH());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Prix_Phase_HT', (float) $phase->getNbrJEH() * $phase->getPrixJEH());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Prix_Phase', (float) $phase->getNbrJEH() * $phase->getPrixJEH());
          if ($phase->getDateDebut())
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Date_Debut', $phase->getDateDebut()->format('d/m/Y'));
          if ($phase->getDateDebut()) {
          $dateFin = clone $phase->getDateDebut(); //WARN $a = $b : $a pointe vers le même objet que $b...
          $dateFin->modify('+' . $phase->getDelai() . ' day');
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Date_Fin', $dateFin->format('d/m/Y'));
          }
          $Delai = $this->jourVersSemaine($phase->getDelai());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Delai', $Delai); //délai en semaine
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Objectif', $phase->getObjectif());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Methodo', $phase->getMethodo());
          $this->array_push_assoc($aides, 'Phase_' . $i . '_Rendu', $validation[$phase->getValidation()]);



          //PVI
          if ($doc == 'PVI' && $i == $phasePVI) {
          $this->array_push_assoc($aides, 'Phase_PVI_Objectif', $phase->getObjectif());
          }
          }

          //DM : Autres dev
          $i = 1;
          $phaseDev = array();
          foreach ($etude->getMissions() as $mission) {

          /**
         * @todo Issue #21

          if ($i == $key + 1) { // Phase concernant l'intervenant


          /* $phaseDev = '';
          foreach ($mission->getPhaseMission()->getValues() as $phaseMission) {
          if ($phaseMission->getNbrJEH())
          $phaseDev[$phaseMission->getPhase()->getPosition() + 1] = $phaseMission->getPhase()->getTitre();
          }
         *  

          //Referent Technique
          if ($mission) {
          if ($refTechnique = $mission->getReferentTechnique()) {
          $this->array_push_assoc($aides, 'Prenom_Referent_Technique', $refTechnique->getPersonne()->getPrenom());
          $this->array_push_assoc($aides, 'Nom_Referent_Technique', $refTechnique->getPersonne()->getNom());
          $this->array_push_assoc($aides, 'Mail_Referent_Technique', $refTechnique->getPersonne()->getEmail());
          $this->array_push_assoc($aides, 'Tel_Referent_Technique', $refTechnique->getPersonne()->getMobile());
          }
          }
          }
          if ($mission) { // Autre intervenants
          if ($intervenant = $mission->getIntervenant())
          if ($intervenant = $intervenant->getPersonne()) {
          $this->array_push_assoc($aides, 'Developpeur_' . $i . '_Nom', $intervenant->getNomFormel());
          $this->array_push_assoc($aides, 'Developpeur_' . $i . '_Mail', $intervenant->getEmail());
          $this->array_push_assoc($aides, 'Developpeur_' . $i . '_Tel', $intervenant->getMobile());
          }
          }
          $i++;
          }
          array_multisort($phaseDev);
          $phaseDevString = "";
          foreach ($phaseDev as $keys => $value)
          $phaseDevString .= ($keys) . ' - ' . $value . '\r\n';
          $this->array_push_assoc($aides, 'Phase_Dev', $phaseDevString);

          //Intervenant
          $mission = $etude->getMissions()->get($key);
          if ($mission) {
          if ($mission->getIntervenant()->getPersonne()) {
          $sexe = ($mission->getIntervenant()->getPersonne()->getSexe() == 'M.' ? 1 : 2 );

          $this->array_push_assoc($aides, 'Nom_Etudiant', $mission->getIntervenant()->getPersonne()->getNom());
          $this->array_push_assoc($aides, 'Prenom_Etudiant', $mission->getIntervenant()->getPersonne()->getPrenom());
          $this->array_push_assoc($aides, 'Sexe_Etudiant', $sexe);
          $this->array_push_assoc($aides, 'Adresse_Etudiant', $mission->getIntervenant()->getPersonne()->getAdresse());
          $this->array_push_assoc($aides, 'Nom_Formel_Etudiant', $mission->getIntervenant()->getPersonne()->getNomFormel());
          }

          $Mission_Remuneration = $mission->getRemuneration();
          $Mission_Nbre_JEH = (int) $Mission_Remuneration['jehRemuneration'];
          $Mission_Montant_JEH_Verse = (float) $Mission_Remuneration['montantRemuneration'];

          $Mission_Nbre_JEH_Lettres = $converter->ConvNumberLetter($Mission_Nbre_JEH);
          $Mission_Montant_JEH_Verse_Lettres = $converter->ConvNumberLetter($Mission_Montant_JEH_Verse, 1);

          $this->array_push_assoc($aides, 'Mission_Nbre_JEH', $Mission_Nbre_JEH);
          $this->array_push_assoc($aides, 'Mission_Nbre_JEH_Lettres', $Mission_Nbre_JEH_Lettres);
          $this->array_push_assoc($aides, 'Mission_Montant_JEH_Verse', $Mission_Montant_JEH_Verse);
          $this->array_push_assoc($aides, 'Mission_Montant_JEH_Verse_Lettres', $Mission_Montant_JEH_Verse_Lettres);

          if ($mission->getFinOm())
          $this->array_push_assoc($aides, 'Date_Fin_Mission', $mission->getFinOm()->format("d/m/Y"));
          }
         */
        return $aides;
    }

    private function array_push_assoc(&$array, $key, $value) {
        $array[$key] = $value;
        return $array;
    }

    private function getEtudeFromID($id_etude) {
        $em = $this->getDoctrine()->getManager();

        //Récupère l'étude avec son id
        if (!$etude = $em->getRepository('mgate\SuiviBundle\Entity\Etude')->find($id_etude))
            throw $this->createNotFoundException('Etude[id=' . $id_etude . '] inexistant');

        return $etude;
    }

    private function getDoctypeAbsolutePathFromName($doc) {
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if (!$documenttype = $em->getRepository('mgate\PubliBundle\Entity\DocumentType')->findOneBy(array('name' => $doc))) {
            $chemin = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/bundles/mgatepubli/document-type/' . $doc . '.docx'; //asset
        } else {
            $chemin = $documenttype->getWebPath(); // on prend le document type qui est uploadé
        }
        return $chemin;
    }

    //Prendre tous les fichiers dans word
    private function getDocxContent($docxFullPath) {
        $zip = new \ZipArchive;
        $templateXML = array();
        if ($zip->open($docxFullPath) === TRUE) {


            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if ((strstr($name, "document") || strstr($name, "header") || strstr($name, "footer")) && !strstr($name, "rels")) {
                    $this->array_push_assoc($templateXML, str_replace("word/", "", $name), $zip->getFromIndex($i));
                }
            }
            $zip->close();
        }
        return $templateXML;
    }

    private function traiterTemplates($templateFullPath, $nombreRepeat, $champs) {
        $templatesXML = $this->getDocxContent($templateFullPath); //rÃ©cup contenu XML
        $templatesXMLTraite = array();

        foreach ($templatesXML as $templateName => $templateXML) {
            $this->repeterBlock($templateXML, $nombreRepeat); //RÃ©pÃ©tion phase
            $this->remplirChamps($templateXML, $champs); //remplissage des champs + phases
            $this->accorder($templateXML); //Accord en nombre /!\ accord en genre ?
            $this->liasons($templateXML); //liaisons de d'
            $this->array_push_assoc($templatesXMLTraite, $templateName, $templateXML);
        }

        return $templatesXMLTraite;
    }

    private function traiterImages(&$templatesXML, $images) {
        $allmatches = array();
        foreach ($templatesXML as $key => $templateXML) {
            $i = preg_match_all('#<!--IMAGE\|(.*?)\|\/IMAGE-->#', $templateXML, $matches);
            while ($i--) {
                $splited = preg_split("#\|#", $matches[1][$i]);
                if (isset($images[$splited[0]])) {
                    if (preg_match("#VAR#", $splited[0])) {
                        $cx = $splited[3];
                        $cy = $images[$splited[0]]['height'] * $cx / $images[$splited[0]]['width'];

                        $cx = round($cx);
                        $cy = round($cy);

                        $replacement = array();
                        preg_match("#wp:extent cx=\"$splited[3]\" cy=\"$splited[4]\".*wp:docPr.*a:blip r:embed=\"$splited[1]\".*a:ext cx=\"$splited[3]\" cy=\"$splited[4]\"#", $templateXML, $replacement);
                        $replacement = $replacement[0];
                        $replacement = preg_replace("#cy=\"$splited[4]\"#", "cy=\"$cy\"", $replacement);
                        $templatesXML[$key] = preg_replace("#wp:extent cx=\"$splited[3]\" cy=\"$splited[4]\".*wp:docPr.*a:blip r:embed=\"$splited[1]\".*a:ext cx=\"$splited[3]\" cy=\"$splited[4]\"#", $replacement, $templateXML);
                    }
                }
                array_push($allmatches, $splited);
            }
        }
        return $allmatches;
    }

    private function publipostageEtude($id_etude, $doc, $key) {
        $key = intval($key);

        $etude = $this->getEtudeFromID($id_etude);
        $chemin = $this->getDoctypeAbsolutePathFromName($doc);
        $nombreRepeat = Array(count($etude->getPhases()), count($etude->getMissions()));
        $champs = $this->getAllChamp($etude, $doc, $key);
        $aides = $this->getAidesEtude($etude, $doc, $key);


        //DEBUG   
        if ($this->container->getParameter('debugEnable')) {
            $path = $this->container->getParameter('pathToDoctype');
            $chemin = $path . $doc . '.docx';
        }

        $templatesXMLtraite = $this->traiterTemplates($chemin, $nombreRepeat, $champs);
        $champsBrut = $this->verifierTemplates($templatesXMLtraite);


        $repertoire = 'tmp';

        //SI DM on prend la ref de RM et ont remplace RM par DM
        if ($doc == 'DM') {
            $doc = 'RM';
            $isDM = true;
        }

        if ($etude->getDoc($doc, $key))
            $refDocx = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc, $key);
        else
            $refDocx = 'ERROR';

        //On remplace DM par RM si DM
        if (isset($isDM) && $isDM)
            $refDocx = preg_replace("#RM#", 'DM', $refDocx);

        $idDocx = $refDocx . '-' . ((int) strtotime("now") + rand());
        copy($chemin, $repertoire . '/' . $idDocx);
        $zip = new \ZipArchive();
        $zip->open($repertoire . '/' . $idDocx);

        $images = array();
        //Gantt
        if ($doc == 'AP' || (isset($isDM) && $isDM)) {
            $chartManager = $this->get('mgate.chart_manager');
            $ob = $chartManager->getGantt($etude, $doc);
            if ($chartManager->exportGantt($ob, $idDocx)) {
                $image = array();
                $image['fileLocation'] = "$repertoire/$idDocx.png";
                $info = getimagesize("$repertoire/$idDocx.png");
                $image['width'] = $info[0];
                $image['height'] = $info[1];
                $images['imageVARganttAP'] = $image;
            }
        }

        //Intégration temporaire
        $imagesInDocx = $this->traiterImages($templatesXMLtraite, $images);
        foreach ($imagesInDocx as $image) {
            $zip->deleteName('word/media/' . $image[2]);
            $zip->addFile($repertoire . '/' . $idDocx . '.png', 'word/media/' . $image[2]);
        }

        foreach ($templatesXMLtraite as $templateXMLName => $templateXMLContent) {
            $zip->deleteName('word/' . $templateXMLName);
            $zip->addFromString('word/' . $templateXMLName, $templateXMLContent);
        }






        $zip->close();

        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $refDocx;


        return array($champsBrut, $aides);
    }

    public function publiposterMultipleEtude($id_etude, $doc) {
        $etude = $this->getEtudeFromID($id_etude);
        $refDocx = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc);
        $idZip = 'ZIP' . $refDocx . '-' . ((int) strtotime("now") + rand());
        $_SESSION['idZip'] = $idZip;

        foreach ($etude->getMissions() as $key => $mission) {
            $this->publipostageEtude($id_etude, $doc, $key);
            $this->telechargerAction('', true);
        }

        $_SESSION['refDocx'] = $refDocx;

        $this->telechargerAction('', false, true);
    }

    /** publication du doc
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function publiposterEtudeAction($id_etude, $doc, $key) {
        if (($doc == 'RM' || $doc == 'DM') && $key == -1)
            $champsBrut = $this->publiposterMultipleEtude($id_etude, $doc);
        else
            $champsBrut = $this->publipostageEtude($id_etude, $doc, $key);

        if (count($champsBrut[0])) {
            return $this->render('mgatePubliBundle:Traitement:index.html.twig', array('nbreChampsNonRemplis' => count($champsBrut[0]), 'champsNonRemplis' => array_unique($champsBrut[0]), 'aides' => $champsBrut[1]));
        } else {

            return $this->telechargerAction($doc);
        }
    }

    /** A nettoyer !!
     * @Secure(roles="ROLE_SUIVEUR")
     */
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
                $zip->addFile('tmp/' . $idDocx, $refDocx . '.docx');
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
                header('Content-disposition: attachment; filename=' . $refDocx . '.docx');
                header('Pragma: no-cache');
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: 0');
                readfile($doc);
                exit();
            }
        } else {
            
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

    private function jourVersSemaine($j) {
        $converter = $this->get('mgate.conversionlettre');

        $jour = $j % 7;
        $semaine = (int) floor($j / 7);

        $jour_str = $converter->ConvNumberLetter($jour);
        $semaine_str = $converter->ConvNumberLetter($semaine);

        $jourVersSemaine = "";
        if ($semaine)
            $jourVersSemaine = $semaine_str . ($semaine > 1 ? " semaines" : "e semaine");
        if ($jour)
            $jourVersSemaine .= ($semaine > 0 ? " et " : "" ) . $jour_str . " jour" . ($jour > 1 ? "s" : "");
        return $jourVersSemaine;
    }

    /* Publipostage documents élèves
     *  
     */

    /** publication du doc
     * @Secure(roles="ROLE_SUIVEUR")
     */
    public function publiposterEleveAction($id_eleve, $doc, $key) {
        $champsBrut = $this->publipostageEleve($id_eleve, $doc, $key);

        if (count($champsBrut[0])) {
            return $this->render('mgatePubliBundle:Traitement:index.html.twig', array('nbreChampsNonRemplis' => count($champsBrut[0]), 'champsNonRemplis' => array_unique($champsBrut[0], SORT_STRING), 'aides' => $champsBrut[1]));
        } else {

            return $this->telechargerAction($doc);
        }
    }

    private function publipostageEleve($id, $doc, $key) {
        $key = intval($key);

        $em = $this->getDoctrine()->getManager();

        if (!$personne = $em->getRepository('mgatePersonneBundle:Personne')->find($id))
            throw $this->createNotFoundException('mgatePersonneBundle:Personne' . '[id=' . $id . '] inexistant');

        $chemin = $this->getDoctypeAbsolutePathFromName($doc);
        $champs = $this->getAllChampEleve($personne, $doc, $key);
        $aides = $this->getAidesEleve($id, $personne->getMembre()->getId() ? $personne->getMembre()->getId() : null, $doc, $key);

        if ($this->container->getParameter('debugEnable')) {
            $path = $this->container->getParameter('pathToDoctype');
            $chemin = $path . $doc . '.docx';
        }

        $templatesXMLtraite = $this->traiterTemplates($chemin, 0, $champs);
        $champsBrut = $this->verifierTemplates($templatesXMLtraite);


        $repertoire = 'tmp';

        //A changer
        $mandat = new \DateTime("now");
        $dateAn = $mandat->format("y");
        $mandat = $mandat->format("m") < 4 ? $dateAn - 8 : $dateAn - 7;
        if ($personne->getMembre())
            $refDocx = '[M-GaTE]' . $mandat . '-' . $doc . '-' . $personne->getMembre()->getIdentifiant();
        //Fin a changer

        $idDocx = $refDocx . '-' . ((int) strtotime("now") + rand());
        copy($chemin, $repertoire . '/' . $idDocx);
        $zip = new \ZipArchive();
        $zip->open($repertoire . '/' . $idDocx);

        foreach ($templatesXMLtraite as $templateXMLName => $templateXMLContent) {
            $zip->deleteName('word/' . $templateXMLName);
            $zip->addFromString('word/' . $templateXMLName, $templateXMLContent);
        }
        $zip->close();

        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $refDocx;

        return array($champsBrut, $aides);
    }

    private function getAllChampEleve($personne, $doc, $key) {
        $champs = array();

        $em = $this->getDoctrine()->getManager();

        // Signataire M-GaTE
        $signataire = $em->getRepository('mgatePersonneBundle:Personne')->getLastMembresByPoste('president')->getQuery()->execute();
        if ($signataire) {
            $signataire = $signataire[0];
            $this->array_push_assoc($champs, 'Fonction_Signataire_Mgate', 'président');
            $this->array_push_assoc($champs, 'Nom_Signataire_Mgate', $signataire->getPrenomNom());
        }

        // Info Personne
        $this->array_push_assoc($champs, 'Nom_Formel_Etudiant', $personne->getNomFormel());
        $this->array_push_assoc($champs, 'Nom_Etudiant', $personne->getNom());
        $this->array_push_assoc($champs, 'Prenom_Etudiant', $personne->getPrenom());
        $this->array_push_assoc($champs, 'Adresse_Fiscale_Etudiant', $personne->getAdresse());
        $this->array_push_assoc($champs, 'Telephone_Etudiant', $personne->getMobile());
        $this->array_push_assoc($champs, 'Sexe_Etudiant', $personne->getSexe() == 'M.' ? 1 : 2);

        // Info Membre
        $membre = $personne->getMembre();

        if ($membre) {
            $this->array_push_assoc($champs, 'Lieu_Naissance_Etudiant', $membre->getLieuDeNaissance());
            if ($membre->getDateDeNaissance())
                $this->array_push_assoc($champs, 'Date_Naissance_Etudiant', $membre->getDateDeNaissance()->format("d/m/Y"));
            $this->array_push_assoc($champs, 'Promotion_Etudiant', (string)$membre->getPromotion()); // Cast pour ne pas formater le nombre
            

            // Info Référence
            //A changer
            $mandat = new \DateTime("now");
            $dateAn = $mandat->format("y");
            $mandat = $mandat->format("m") < 4 ? $dateAn - 8 : $dateAn - 7;

            foreach (array('CE', 'AC') as $doctype)
                $this->array_push_assoc($champs, 'Reference_' . $doctype, '[M-GaTE]' . $mandat . '-' . $doctype . '-' . $membre->getIdentifiant());

            foreach($membre->getMandats() as $mandat){
                if($mandat->getPoste()->getIntitule() == "Membre")
                    $lastMemberMandat = $mandat;
            }
            if(isset($lastMemberMandat) && $lastMemberMandat->getDebutMandat()){
                $this->array_push_assoc($champs, 'Date_Signature', $lastMemberMandat->getDebutMandat()->format("d/m/Y"));
                $this->array_push_assoc($champs, 'Date_Cheque', $lastMemberMandat->getDebutMandat()->format("d/m/Y"));
            }
        }
        return $champs;
    }

    private function getAidesEleve($personne_id, $membre_id, $doc, $key) {
        $aides = array();
        $router = $this->get('router');

        $this->array_push_assoc($aides, 'Fonction_Signataire_Mgate', $router->generate('mgatePersonne_membre_homepage', array(), true));
        $this->array_push_assoc($aides, 'Nom_Signataire_Mgate', $router->generate('mgatePersonne_membre_homepage', array(), true));

        // Info Personne
        $this->array_push_assoc($aides, 'Nom_Formel_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));
        $this->array_push_assoc($aides, 'Nom_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));
        $this->array_push_assoc($aides, 'Prenom_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));
        $this->array_push_assoc($aides, 'Adresse_Fiscale_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));
        $this->array_push_assoc($aides, 'Telephone_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));

        // Info Membre
        $this->array_push_assoc($aides, 'Lieu_Naissance_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));
        $this->array_push_assoc($aides, 'Date_Naissance_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));
        $this->array_push_assoc($aides, 'Promotion_Etudiant', $router->generate('mgatePersonne_membre_voir', array('id' => $membre_id), true));

        /*        // Info Référence
          //A changer
          foreach (array('CE','AC') as $doctype)
          $this->array_push_assoc($aides, 'Reference_'.$doctype, '[M-GaTE]'.$mandat.'-'.$doctype.'-'.$membre->getIdentifiant());

          /**
         * @todo
         *
          $date = new \DateTime("now");
          $this->array_push_assoc($aides, 'Date_Signature', $date->format("d/m/Y"));
          $this->array_push_assoc($aides, 'Date_Cheque', $date->format("d/m/Y"));
         */
        return $aides;
    }

}
