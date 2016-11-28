<?php

/**
 * Description of CoincidenceController
 *
 * @author ppd
 */
class CoincidenceController extends Controller
{
    public function saveAction()
    {
        $coincidence_id = $this->getEntityManager()->getModel('sais:Coincidence')->calculateNextID();
        $conclusion_id = $this->post['conclusion_id'];
        $condition_id = $this->post['condition_id'];
        $presence = $this->post['presence'];        
        $what = [
            'coincidence_id' => $coincidence_id,
            'ConclusionID' => $conclusion_id,
            'ConditionID' => $condition_id,
            'presence' => $presence,
        ];
        $queryresult = $this->getEntityManager()->getModel('sais:Coincidence')->insert($what);
        exit(json_encode($queryresult));
    }
    
    public function addAction()
    {
        echo $this->render('Coincidence/add.html.twig');
    }
    
    public function addconclusionAction()
    {
        $conclusion_id = $this->getEntityManager()->getModel('sais:Conclusion')->calculateNextID();
        $conclusion_name = '"'.$this->post['conclusion_name'].'"';
        $what = [
            'conclusion_id' => $conclusion_id,
            'conclusion_name' => $conclusion_name,
        ];
        $queryresult = $this->getEntityManager()->getModel('sais:Conclusion')->insert($what);
        exit(json_encode($queryresult));
    }

    public function addconditionAction()
    {
        $condition_id = $this->getEntityManager()->getModel('sais:Condition')->calculateNextID();
        $condition_name = '"'.$this->post['condition_name'].'"';
        $what = [
            'condition_id' => $condition_id,
            'condition_name' => $condition_name,
        ];
        $queryresult = $this->getEntityManager()->getModel('sais:Condition')->insert($what);
        exit(json_encode($queryresult));
    }
    
    public function editconclusionAction($id)
    {
        $em = $this->getEntityManager();
        $coincidence = $em->getModel('sais:Coincidence')->findOne($id);
        $what = [
            'conclusion_name' => '"'.$this->post['new_conclusion_name'].'"',
        ];
        $criteria = [
            'conclusion_id' => $coincidence['ConclusionID'],
        ];
        $conclusion = $em->getModel('sais:Conclusion')->update($what, $criteria);
        exit(json_encode($conclusion));
    }
    
    public function editconditionAction($id)
    {
        $em = $this->getEntityManager();
        $coincidence = $em->getModel('sais:Coincidence')->findOne($id);
        $what = [
            'condition_name' => '"'.$this->post['new_condition_name'].'"',
        ];
        $criteria = [
            'condition_id' => $coincidence['conditionid'],
        ];
        $condition = $em->getModel('sais:Condition')->update($what, $criteria);
        exit(json_encode($condition));
    }
    
    public function prepareeditAction($id)
    {
        $coincidence = $this->getEntityManager()->getModel('sais:Coincidence')->findOne($id);
        $conclusions = $this->getEntityManager()->getModel('sais:Conclusion')->findAll();
        $conditions = $this->getEntityManager()->getModel('sais:Condition')->findAll();
        foreach($conclusions as $conclusion) {
            if($conclusion['conclusion_id'] == $coincidence['ConclusionID']) {
                $conclusionIdBefore = $coincidence['ConclusionID'];
            }
        }
        foreach($conditions as $condition) {
            if($condition['condition_id'] == $coincidence['ConditionID']) {
                $conditionIdBefore = $coincidence['ConditionID'];
            }
        }
        $presence = $coincidence['presence'];
        
        echo $this->render('Coincidence/edit.html.twig', [
            'coincidence_id' => $id,
            'conclusions' => $conclusions,
            'selectedConclusion' => $conclusionIdBefore,
            'conditions' => $conditions,
            'selectedCondition' => $conditionIdBefore,
            'presence' => $presence,
        ]);
    }
    
    public function editAction($id)
    {
        $what = [
            'conclusionid' => $this->post['new_conclusion_id'],
            'conditionid' => $this->post['new_condition_id'],
            'presence' => $this->post['new_presence'],
        ];
        $criteria = [
            'coincidence_id' => $id,
        ];
        $this->getEntityManager()->getModel('sais:Coincidence')->update($what, $criteria);
        $this->redirect('/sais/coincidence/prepareedit/'.$id);
    }
    
    public function deleteAction($id)
    {
        $criteria = [
            'coincidence_id' => $id,
        ];
        $deleted = $this->getEntityManager()->getModel('sais:Coincidence')->delete($criteria);
        exit(json_encode($deleted));
    }
    
    public function deleteconclusionAction($id)
    {
        $em = $this->getEntityManager();
        $coincidence = $em->getModel('sais:Coincidence')->findOne($id);
        $coincidenceCriteria = [
            'conclusionid' => $coincidence['conclusionid'],
        ];
        $em->getModel('sais:Coincidence')->delete($coincidenceCriteria);
        $conclusionCriteria = [
            'conclusion_id' => $coincidence['conclusionid'],
        ];
        $conclusionDeleted = $em->getModel('sais:Conclusion')->delete($conclusionCriteria);
        exit(json_encode($conclusionDeleted));
    }
    
    public function deleteconditionAction($id)
    {
        $em = $this->getEntityManager();
        $coincidence = $em->getModel('sais:Coincidence')->findOne($id);
        $coincidenceCriteria = [
            'conditionid' => $coincidence['conditionid'],
        ];
        $em->getModel('sais:Coincidence')->delete($coincidenceCriteria);
        $conditionCriteria = [
            'condition_id' => $coincidence['conditionid'],
        ];
        $conditionDeleted = $em->getModel('sais:Condition')->delete($conditionCriteria);
        exit(json_encode($conditionDeleted));
    }
    
    
    public function showAction()
    {
        if(!empty($this->post['ordering'])) $orderBy = $this->post['ordering']; 
        else $orderBy = ['coincidence_id' => 'ASC', 'conclusion_name' => 'DESC', 'condition_name' => 'ASC'];
        $coincidences = $this->getEntityManager()->getModel('sais:Coincidence')->selectCoincidencesRelated($orderBy);
        echo $this->render("Coincidence/show.html.twig", ['coincidences' => $coincidences]);
    }
}
