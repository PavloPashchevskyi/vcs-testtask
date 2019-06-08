<?php

namespace Application\Modules\Main\Controllers;

use Application\Core\Controller;

/**
 * Description of DefaultController
 *
 * @author ppd
 */
class DefaultController extends Controller
{
    public function indexAction()
    {
        echo $this->view->render('Default/index.html.twig', ['welcome' => 'hello word']);
    }
    
    public function infoAction()
    {
        phpinfo();
    }
}
