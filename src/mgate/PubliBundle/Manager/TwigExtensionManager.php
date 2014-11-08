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


namespace mgate\PubliBundle\Manager;

class TwigExtensionManager extends \Twig_Extension {
    public function getName() {
        return 'mgate_publibundle_twigextensionmanager';
        
    }
    
    public function getFunctions() {
        return array(
        );
    }
    
    public function getFilters() {
        return array(
            'nl2wbr'        => new \Twig_Filter_Method($this, 'nl2wbr'),
            'money'         => new \Twig_Filter_Method($this, 'money'),
            'nbrToLetters'  => new \Twig_Filter_Method($this, 'nbrToLetters'),
            'liaison'       => new \Twig_Filter_Method($this, 'liaison'),
            'pluriel'       => new \Twig_Filter_Method($this, 'pluriel'),
        );
    }
    
    public function nl2wbr($input) {        
        return preg_replace('#(\\r\\n)|(\\n)|(\\r)#', '<w:br />', $input);
    }
    
    public function money($input, $displayZero = true) {    
        if($input == 0 && !$displayZero) return '';
        return number_format($input, 2, ',', ' ');
    }
    
    /**
     * fonction permettant de transformer une valeur numérique en valeur en lettre
     * @param int $nbr le nombre a convertir
     * @param int $devise (0 = aucune, 1 = Euro €, 2 = Dollar $)
     * @param int $langue (0 = Français, 1 = Belgique, 2 = Suisse)
     * @return string la chaine
     */
    public function nbrToLetters($nbr, $devise = 0, $langue = 0) {        
        $cv = new \mgate\PubliBundle\Controller\ConversionLettreController;
        return $cv->ConvNumberLetter($nbr, $devise, $langue);
    }
    
    public function liaison($mot, $entiere = 'de', $contractee = null){
        if(!$contractee)
            $contractee = substr ($entiere, 0, 1)."'";
        
        if(preg_match('#^[aeiouy]#', $mot))
            return $contractee.' '.$mot;
        else
            return $entiere.' '.$mot;
        
    }
    
    public function pluriel($nbr, $pluriel = 's', $simple = ''){
        if($nbr > 1)
            return $pluriel;
        else
            return $simple;
    }
    
}
