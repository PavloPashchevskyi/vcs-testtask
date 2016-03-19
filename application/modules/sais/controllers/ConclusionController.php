<?php

/**
 * Description of ConclusionController
 *
 * @author ppd
 */
class ConclusionController extends Controller
{
    public function fromconditionsAction()
    {
        //from client
        $presentConditions = [];
        $i = 0;
        foreach($this->request as $condition_name => $on) {
            if($condition_name != 'submitinterview') {
                $presentConditions[$i]['condition_name'] = str_replace('_', ' ', $condition_name);
                $presentConditions[$i]['presence'] = ($on == 'on') ? 1 : 0;
            }
            $i++;
        }
        $conditionNames = [];
        foreach($presentConditions as $i => $presentCondition) {
            $conditionNames[$i] = $presentCondition['condition_name'];
        }
        $condition = $this->getEntityManager()->getModel('sais:Condition'); 
        $absentConditionsFromDB = $condition->selectAbsent($conditionNames);
        $absentConditions = [];
        foreach($absentConditionsFromDB as $i => $absentCondition) {
            $absentConditions[$i]['condition_name'] = $absentCondition['condition_name'];
            $absentConditions[$i]['presence'] = 0;
        }
        $conditionsFromClient = array_merge($presentConditions, $absentConditions);
        asort($conditionsFromClient);
        //from database
        $conclusionsFromDB = $this->getEntityManager()->getModel('sais:Conclusion')->findAll();
        $conclusionConditions = [];
        $finalConclusions = [];
        foreach($conclusionsFromDB as $i => $conclusionFromDB) {
            $conclusionName = $conclusionFromDB['conclusion_name'];
            $conclusionConditions[$i] = $this->getEntityManager()->getModel('sais:Coincidence')->selectConclusionConditions($conclusionName);
            $j = 0;
            $k = 0;
            foreach($conditionsFromClient as $conditionFromClient) {
                if($conclusionConditions[$i][$j]['presence'] == $conditionFromClient['presence']) {
                    $k++;
                }
                $j++;
            }
            if($k == count($conclusionConditions[$i])) {
                $conditionNumber = $k-1;
                $finalConclusions[] = $conclusionConditions[$i][$conditionNumber]['conclusion_name'];
            }
        }
        exit(json_encode($finalConclusions));
    }
    
    public function addAction()
    {
        $what = $this->post;
        $this->getEntityManager()->getModel('sais:Conclusion')->insert($what);
        echo $this->redirect('/sais/coincidence/add');
    }
    
    public function editAction()
    {
        $where = $this->post;
        $this->getEntityManager()->getModel('sais:Conclusion')->update($what, $where);
        echo $this->redirect('/sais/coincidence/add');
    }
    
    public function deleteAction()
    {
        
    }
    
    public function selectAction()
    {
        $conclusions = $this->getEntityManager()->getModel('sais:Conclusion')->findAll();
        exit(json_encode($conclusions));
    }
}
