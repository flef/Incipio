<?php

namespace mgate\PubliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TraitementController extends Controller {

    //Repétition des phases
    private function repeterPhase($templateXML, $nombrePhase) {

        $regexRepeatSTART = '<w:bookmarkStart w:id="\d+" w:name="repeatSTART"/>\s*\S*<w:bookmarkEnd w:id="\d+"/>'; //Marqueur de début de repeat
        $regexRepeatEND = '<w:bookmarkStart w:id="\d+" w:name="repeatEND"/>\s*\S*<w:bookmarkEnd w:id="\d+"/>'; //Marqueur de fin de repeat
        $regexpRepeat = '#' . $regexRepeatSTART . '(.*?)' . $regexRepeatEND . '#s'; // *? see ungreedy behavior //Expression régulière filtrage répétition /!\ imbrication interdite !

        $callback = function ($matches) use ($nombrePhase) {//Fonction de callback prétraitement de la zone à répéter
                    $outputString = "";
                    for ($i = 1; $i <= $nombrePhase; $i++)
                        $outputString .= preg_replace('#%Phase_Index%#', "$i", $matches[1]);
                    return $outputString;
                };

        return preg_replace_callback($regexpRepeat, $callback, $templateXML);
    }

    //Remplissage des %champs%
    private function remplirChamps($templateXML, $fieldValues) {

        foreach ($fieldValues as $field => $values)//Remplacement des champs
        {
            if($values != NULL)
            {
                 $templateXML = preg_replace('#' . $field . '#', $values, $templateXML);
            }
        }
           

        return $templateXML;
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
    private function accorder($templateXML) {
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

        return preg_replace_callback($regexp, $callback, $templateXML);
    }

    //Traitement du template
    private function traiterTemplate($templateFullPath, $nombrePhase, $champs) {
        $templateXML = file_get_contents($templateFullPath); //récup contenu XML
        // TODO &$templateXML 
        $templateXML = repeterPhase($templateXML, $nombrePhase); //Répétion phase
        $templateXML = remplirChamps($templateXML, $champs); //remplissage des champs + phases
        $templateXML = accorder($templateXML); //Accord en nombre /!\ accord en genre ?

        return $templateXML;
    }

    //Téléchargement du fichier
    private function telechargerDocType($templateXML) {
        //écriture fichier sur disque TODO : dl
        $newFile = fopen("./tests.xml", "w+");
        fwrite($newFile, $doc);
        fclose($newFile);
    }

    //Vérification du fichier
    //if match %   _   % then pasbien
    private function verifierTemplate($templateXML)
    {

    }

    //publication du doc
    public function publiposterAction() {

        //$champs = etude->getChamps($doctype = AP || AV.... )
        $templateXMLtraite = traiterTemplate($template . '.xml', $nombrePhase, $champs);
        telechargerDocType($templateXMLtraite);
    }

    private function getAllChamp($etude) {
        $etude = new \mgate\SuiviBundle\Entity\Etude();//Juste pour avoir l'autocompletion :D
        $champs = Array(
            "%Total_HT_Lettres%" => $etude,
            "%TVA%" => 19.6,
            "%Montant_TVA%" => $Montant_TVA,
            "%Montant_TVA_Lettres%" => $Montant_TVA_Lettres,
            "%Total_TTC%" => $Total_TTC,
            "%Total_TTC_Lettres%" => $Total_TTC_Lettres,
            "%Entite_Sociale%" => $etude->getProspect()->getEntite(),
            "%Adresse_Client%" => $etude->getProspect()->getAdresse(),
            "%Nom_Signataire%" => $etude->getAp()->getSignataire2()->getPrenomNom(),
            "%Fonction_Signataire%" => $etude->getAp()->getSignataire2()->getPoste(),
            "%Description_Prestation%" => $etude->getDescriptionPrestation(),
            "%Delais_Semaines%" => $Delais_Semaines,
            "%Total_HT%" => $Total_HT,
            "%Nbr_JEH_Total%" => $Nbr_JEH_Total,
            "%Nbr_JEH_Total_Lettres%" => $Nbr_JEH_Total_Lettres,
            "%Montant_Total_HT%" => $Montant_Total_HT,
            "%Montant_Total_HT_Lettres%" => $Montant_Total_HT_Lettres,
            "%Frais_HT%" => $Frais_HT,
            "%Frais_HT_Lettres%" => $Frais_HT_Lettres,
            "%Acompte_HT%" => $Acompte_HT,
            "%Acompte_HT_Lettres%" => $Acompte_HT_Lettres,
            "%Acompte_TTC%" => $Acompte_TTC,
            "%Acompte_TTC_Lettres%" => $Acompte_TTC_Lettres,
            "%Solde_PVR_HT%" => $Solde_PVR_HT,
            "%Solde_PVR_HT_Lettres%" => $Solde_PVR_HT_Lettres,
            "%Solde_PVR_TTC%" => $Solde_PVR_TTC,
            "%Solde_PVR_TTC_Lettres%" => $Solde_PVR_TTC_Lettres,
            "%Total_TVA%" => $Total_TVA,
            "%Acompte_TVA%" => $Acompte_TVA,
            "%Acompte_Pourcentage%" => $Acompte_Pourcentage,
            "%Date_Emission%" => $Date_Emission,
            "%Date_Limite%" => $Date_Limite,
            "%Reference_PVR%" => $Reference_PVR,
            "%Date_Debut%" => $Date_Debut,
            "%Date_Fin%" => $Date_Fin,
            "%Reference_Etude%" => $Reference_Etude,
            "%Reference_CC%" => $Reference_CC,
            "%Reference_AP%" => $Reference_AP,
            "%Reference_OM%" => $Reference_OM,
            "%Reference_CE%" => $Reference_CE,
            "%Nom_Etudiant%" => $Nom_Etudiant,
            "%Prenom_Etudiant%" => $Prenom_Etudiant,
            "%Sexe%" => $Sexe,
            "%Adresse_Etudiant%" => $Adresse_Etudiant,
            "%Montant_JEH_Verse%" => $Montant_JEH_Verse,
            "%Montant_JEH_Verse_Lettres%" => $Montant_JEH_Verse_Lettres,
            "%Nbre_JEH%" => $Nbre_JEH,
            "%Nbre_JEH_Lettres%" => $Nbre_JEH_Lettres,
            "%Remuneration_Brut%" => $Remuneration_Brut,
            "%Remuneration_Brut_Lettres%" => $Remuneration_Brut_Lettres,
            "%Date_Fin_Etude%" => $Date_Fin_Etude,
            "%Nom_Client%" => $Nom_Client,
            "%Description_Prestation%" => $Description_Prestation,
            
            "%Nbr_JEH_Total%" => 6,
            "%Nbr_Developpeurs%" => 2,
            "%Nbr_Phases%" => 5,
            "%Phase_1_Titre%" => "Titre de la phase 1 :D",
            "%Phase_1_Nbre_JEH%" => $Phase_1_Nbre_JEH,
            "%Phase_1_Prix_JEH_HT%" => $Phase_1_Prix_JEH_HT,
            "%Phase_1_Prix_Phase_HT%" => $Phase_1_Prix_Phase_HT,
            );

        return $champs;
    }

}
