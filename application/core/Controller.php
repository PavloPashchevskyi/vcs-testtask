<?php

namespace Application\Core;

/**
 * Description of Controller
 *
 * @author ppd
 */
class Controller
{
    
    private $entityManager;
    protected $request;
    protected $get;
    protected $post;
    protected $view;

    public function __construct()
    {
        $this->entityManager = new EntityManager();
        $this->request = $_REQUEST;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->view = new View();
    }
    
    protected function getEntityManager()
    {
        return $this->entityManager;
    }
    
    protected function redirect($route)
    {
        header('Location: '.$route);
    }
}
