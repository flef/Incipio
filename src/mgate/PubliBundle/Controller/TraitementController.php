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
    private function remplirChamps($templateXML, $fieldValues, $phases) {

        foreach ($fieldValues as $field => $values)//Remplacement des champs hors phases
            $templateXML = preg_replace('#' . $field . '#', $values, $templateXML);
        foreach ($phases as $field => $values)//remplacement des phases
            $templateXML = preg_replace('#' . $field . '#', $values, $templateXML);

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
    private function traiterTemplate($templateFullPath, $fields) {
        $templateXML = file_get_contents($templateFullPath); //récup contenu XML
        // TODO &$templateXML 
        $templateXML = repeterPhase($templateXML, $fields->nombrePhase); //Répétion phase
        $templateXML = remplirChamps($templateXML, $fields->champs, $fields->phases); //remplissage des champs + phases
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
    //publication du doc
    public function publiposter($etude, $docType) {
        
        
        //$champs = etude->getChamps($doctype = AP || AV.... )
        $templateXMLtraite = traiterTemplate($template.'.xml', $champs);
        telechargerDocType($templateXMLtraite);
    }




}
