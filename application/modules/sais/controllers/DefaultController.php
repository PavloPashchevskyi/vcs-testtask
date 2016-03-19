<?php

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
        exit($this->render('common/base.html.twig', $data));
    }
}
