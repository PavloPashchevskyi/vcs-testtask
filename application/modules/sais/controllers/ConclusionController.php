<?php

namespace Application\Modules\Sais\Controllers;

use Application\Core\Controller;

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
        $presentConditionsNames = [];
        $i = 0;
        foreach($this->request as $condition_name => $on) {
            if($condition_name !== 'submitinterview') {
                $presentConditions[$i]['condition_name'] = $presentConditionsNames[$i] =
                    str_replace('_', ' ', $condition_name);
                $presentConditions[$i]['presence'] = ($on === 'on') ? 1 : 0;
            }
            $i++;
        }
        /** @var \Application\Core\EntityManager $em */
        $em = $this->getEntityManager();
        /** @var \Application\Modules\Sais\Models\Condition[] $absentConditions */
        $absentConditions = $em->getModel('sais:Condition')->selectAbsent($presentConditionsNames);
        $absentConditions = array_map(function ($item) {
            $item['presence'] = 0;
            return $item;
        }, $absentConditions);
        $inputConditions = array_merge($presentConditions, $absentConditions);
        usort($inputConditions, function ($inputCondition1, $inputCondition2) {
            return strcmp($inputCondition1['condition_name'], $inputCondition2['condition_name']);
        });
        /** @var \Application\Modules\Sais\Models\Conclusion $conclusionModel */
        $conclusions = $em->getModel('sais:Conclusion')->findAll();
        /** @var \Application\Modules\Sais\Models\Coincidence $coincidenceModel */
        $coincidenceModel = $em->getModel('sais:Coincidence');
        $latestConclusion = [];
        foreach ($conclusions as $conclusion) {
            /** @var \Application\Modules\Sais\Models\Coincidence[] $coincidencesRelated */
            $coincidencesRelated = $coincidenceModel->selectConclusionConditions($conclusion['conclusion_name']);
            foreach ($coincidencesRelated as $coincidenceRelated) {
                foreach ($inputConditions as $inputCondition) {
                    if (
                        $coincidenceRelated['condition_name'] === $inputCondition['condition_name'] &&
                        $coincidenceRelated['presence'] === (bool) $inputCondition['presence']
                    ) {
                        $latestConclusion[] = $coincidenceRelated['conclusion_name'];
                    }
                }
            }
        }
        $conclusionsCounts = array_count_values($latestConclusion);
        $result = [];
        foreach ($conclusionsCounts as $conclusion => $conclusionCount) {
            if ($conclusionCount === count($inputConditions)) {
                $result[] = $conclusion;
            }
        }
        exit(\json_encode($result));
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
