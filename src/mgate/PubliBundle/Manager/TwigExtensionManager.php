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
        );
    }
    
    public function nl2wbr($input) {        
        return preg_replace('#(\\r\\n)|(\\n)|(\\r)#', '<w:br />', $input);
    }
}
