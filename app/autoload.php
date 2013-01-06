<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
    $loader->add('CoreSphere', __DIR__.'/../vendor/bundles');
    
    $loader->add('Io\\FormBundle', __DIR__.'/../vendor/io/form-bundle'); 

}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

ini_set('xdebug.max_nesting_level', 200); // cf README

return $loader;
