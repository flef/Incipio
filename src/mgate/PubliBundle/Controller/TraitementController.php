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


        $regexRepeatSTART = '<w:bookmarkStart w:id="\d+" w:name="repeatSTART"/>\s*\S*<w:bookmarkEnd w:id="\d+"/>'; //Marqueur de début de repeat
        $regexRepeatEND = '<w:bookmarkStart w:id="\d+" w:name="repeatEND"/>\s*\S*<w:bookmarkEnd w:id="\d+"/>'; //Marqueur de fin de repeat
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
                $templateXML = preg_replace('#' . $SFD . $field . $EFD . '#U', $values, $templateXML);
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

    //Traitement du template
    private function traiterTemplate($templateFullPath, $nombrePhase, $champs) {
        $templateXML = file_get_contents($templateFullPath); //récup contenu XML

        $this->repeterPhase($templateXML, $nombrePhase); //Répétion phase
        $this->remplirChamps($templateXML, $champs); //remplissage des champs + phases
        $this->accorder($templateXML); //Accord en nombre /!\ accord en genre ?

        return $templateXML;
    }

    //Vérification du fichier
    //if match %   _   % then pasbien
    private function verifierTemplate($templateXML) {
        $SFD = $this->SFD;
        $EFD = $this->EFD;

        preg_match_all('#' . $SFD . '(.*?)' . $EFD . '#', $templateXML, $matches);

        return $matches[1];
    }

    private function getAllChamp($etude) {

        $phases = $etude->getPhases();
        $nombrePhase = count($phases);

        $Total_HT = $this->get('mgate.etude_manager')->getTotalJEHHT($etude);
        $Montant_Total_HT = $this->get('mgate.etude_manager')->getTotalHT($etude);
        $Total_TTC = $this->get('mgate.etude_manager')->getTotalTTC($etude);

        $Total_HT_Lettres = $this->get('mgate.conversionlettre')->ConvNumberLetter($Total_HT, 1);
        $Montant_Total_HT_Lettres = $this->get('mgate.conversionlettre')->ConvNumberLetter($Montant_Total_HT, 1);
        $Total_TTC_Lettres = $this->get('mgate.conversionlettre')->ConvNumberLetter($Total_TTC, 1);

        $Nbr_JEH = $this->get('mgate.etude_manager')->getNbrJEH($etude);
        $Nbr_JEH_Lettres = $this->get('mgate.conversionlettre')->ConvNumberLetter($Nbr_JEH);
        
        $Frais_HT = $etude->getFraisDossier();
        $Frais_HT_Lettres = $this->get('mgate.conversionlettre')->ConvNumberLetter($Frais_HT);


        $champs = Array(
            'Presentation_Projet' => $etude->getPresentationProjet(),
            'Description_Prestation' => $etude->getDescriptionPrestation(),
            'Type_Prestation' => $etude->getTypePrestation(),
            //'Date_Lancement' => $etude->getDateDebut()->format("m"),
            //'Date_Fin'=> $etude->getDateFin()->format("m"),

            'Nbr_JEH_Total' => $Nbr_JEH,
            'Nbr_JEH_Total_Lettres' => $Nbr_JEH_Lettres,
            
            'Total_HT' => $Total_HT,
            'Montant_Total_HT' => $Montant_Total_HT,
            'Total_TTC' => $Total_TTC,
            
            'Total_HT_Lettres' => $Total_HT_Lettres,
            'Montant_Total_HT_Lettres' => $Montant_Total_HT_Lettres,
            'Total_TTC_Lettres' => $Total_TTC_Lettres,
            
            'Frais_HT' => $Frais_HT,
            'Frais_HT_Lettres' => $Frais_HT_Lettres,
            
            'Nbr_Phases' => $nombrePhase,
            
        );



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

            if ($etude->getSuiveur()->getFix() != NULL)
                $this->array_push_assoc($champs, 'Tel_suiveur', $etude->getSuiveur()->getFix());
            else
                $this->array_push_assoc($champs, 'Tel_suiveur', $etude->getSuiveur()->getMobile());
        }


        //Avant-Projet
        if ($etude->getAp() != NULL) {

            //Signataire 1
            if ($etude->getAp()->getSignataire1() != NULL)
                $this->array_push_assoc($champs, 'Nom_Client', $etude->getAp()->getSignataire1()->getPrenomNom());
            //Signataire 2
            if ($etude->getAp()->getSignataire2() != NULL) {
                $this->array_push_assoc($champs, 'Nom_Signataire', $etude->getAp()->getSignataire2()->getPrenomNom());
                $this->array_push_assoc($champs, 'Fonction_Signataire', $etude->getAp()->getSignataire2()->getPoste());
            }            
            //Date Signature
            if($etude->getAp()->getDateSignature() != NULL)
                $this->array_push_assoc($champs, 'Date_Signature_AP', $etude->getAp()->getDateSignature()->format("d/m/Y"));
            //Référence AP
            $referenceAP = $this->get('mgate.etude_manager')->getRefEtude($etude) . '-AP-' . $etude->getAp()->getVersion();
            $this->array_push_assoc($champs, 'Reference_AP', $referenceAP);
        }


        //Phases
        foreach ($phases as $phase) {
            $i = $phase->getPosition() + 1;

            $this->array_push_assoc($champs, 'Phase_' . $i . '_Titre', $phase->getTitre());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Nbre_JEH', $phase->getNbrJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_JEH_HT', $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_Phase_HT', $phase->getNbrJEH() * $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Prix_Phase', $phase->getNbrJEH() * $phase->getPrixJEH());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Date_Debut', $phase->getDateDebut()->format('d/m/Y'));
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Delai', $phase->getDelai());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Objectif', $phase->getObjectif());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Methodo', $phase->getMethodo());
            $this->array_push_assoc($champs, 'Phase_' . $i . '_Rendu', $phase->getValidation());
        }

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
            echo 'DocumentType[name=' . $doc . '] non trouvé: on utilise un asset<br /><br />';
            $chemin = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/bundles/mgatepubli/document-type/' . $doc . '.xml';
            //throw $this->createNotFoundException('DocumentType[name=' . $doc . '] inexistant');
        } else {
            echo 'DocumentType uploadé trouvé<br /><br />';
            $chemin = $documenttype->getWebPath();
        }
        return $chemin;
    }

    //publication du doc
    public function publiposterAction($id_etude, $doc) {



        $etude = $this->getEtudeFromID($id_etude);
        $chemin = $this->getDoctypeAbsolutePathFromName($doc);
        $nombrePhase = count($etude->getPhases());
        $champs = $this->getAllChamp($etude);


        //debug
        if (false)
            $chemin = 'C:\wamp\www\My-M-GaTE\src\mgate\PubliBundle\Resources\public\document-type/' . $doc . '.xml';
        if (false)
            $chemin = 'C:\Users\flo\Desktop\DocType Fonctionnel/FA.xml';

        $templateXMLtraite = $this->traiterTemplate($chemin, $nombrePhase, $champs); //Ne sais ou mettre mes ressources


        $champsBrut = $this->verifierTemplate($templateXMLtraite);

        $repertoire = 'tmp';
        $idDocx = (int) strtotime("now") + rand();
        if (!file_exists($repertoire))
            mkdir($repertoire/* ,0700 */);
        $handle = fopen($repertoire . '/' . $idDocx, "w+");
        fwrite($handle, $templateXMLtraite);
        fclose($handle);

        $_SESSION['idDocx'] = $idDocx;
        $_SESSION['refDocx'] = $this->get('mgate.etude_manager')->getRefDoc($etude, $doc, 1);

        return $this->render('mgatePubliBundle:Traitement:index.html.twig', array('champsNonRemplis' => $champsBrut));
    }

    public function telechargerAction($docType = 'AP') {

        if (isset($_SESSION['idDocx']) && preg_split('#.#', $_SESSION['idDocx'][1][0] == 'xml')) {
            $idDocx = $_SESSION['idDocx'];
            $refDocx = (isset($_SESSION['refDocx']) ? $_SESSION['refDocx'] : $docType);

            $doc = 'tmp/' . $idDocx;

            header('Content-Type: application/msword');
            header('Content-Length: ' . filesize($doc));
            header('Content-disposition: attachment; filename=' . $doc);
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            readfile($doc);
            exit();
        } else {
            echo 'fail';
        }
        return $this->render('mgatePubliBundle:Default:index.html.twig', array('name' => 'ololilioloiol'));
    }

}

