<?php

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
            'nl2wbr' => new \Twig_Filter_Method($this, 'nl2wbr'),
            'money' => new \Twig_Filter_Method($this, 'money'),
            'nbrToLetters' =>  new \Twig_Filter_Method($this, 'nbrToLetters'),
        );
    }
    
    public function nl2wbr($input) {        
        return preg_replace('#(\\r\\n)|(\\n)|(\\r)#', '<w:br />', $input);
    }
    
    public function money($input) {        
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
    
}
