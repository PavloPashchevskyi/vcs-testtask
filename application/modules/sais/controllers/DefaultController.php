<?php

namespace Application\Modules\Sais\Controllers;

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
        $data = [
            'welcome' => 'Hello word!',
        ];
        //$this->view->generate('main_view.php', null, $data);
        exit($this->view->render('common/base.html.twig', $data));
    }
}
