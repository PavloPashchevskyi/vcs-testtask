<?php

namespace Application\Core;

/**
 * Description of View
 *
 * @author ppd
 */
class View extends \Twig_Environment
{
    public function __construct()
    {
        $moduleName = Application::getModuleName();
        $loader = new \Twig_Loader_Filesystem('application/modules/'.$moduleName.'/views');
        parent::__construct($loader, array(
            'cache' => 'var/cache',
        ));
    }
}
