<?php

/**
 * Description of ConditionController
 *
 * @author ppd
 */
class ConditionController extends Controller
{

    public function interviewAction()
    {
        $conditions = $this->getEntityManager()->getModel('sais:Condition')->findAll();
        $data = [
            'conditions' => $conditions,
        ];
        exit($this->render('Condition/interview.html.twig', $data));
    }
    
    public function addAction()
    {
        
    }
    
    public function editAction()
    {
        
    }
    
    public function deleteAction()
    {
        
    }
    
    public function selectAction()
    {
        $conditions = $this->getEntityManager()->getModel('sais:Condition')->findAll();
        exit(json_encode($conditions));
    }
}
