<?php

namespace Application\Core;

use Application\Core\Exceptions;

/**
 * Description of EntityManager
 *
 * @author ppd
 */
class EntityManager 
{
    private $model;
    
    public function getModel($modelPath)
    {
        $modulePathParts = explode(':', $modelPath);
        $moduleName = strtolower($modulePathParts[0]);
        $modelName = ucfirst($modulePathParts[1]);
        $modelFullQualifiedName = "\\Application\\Modules\\".$moduleName."\\Models\\".$modelName;
        if(class_exists($modelFullQualifiedName)) {
            $this->model = new $modelFullQualifiedName();
            if (!$this->model) {
                throw new Exceptions\ModelClassNotDefinedException($moduleName, $modelName);
            }
            return $this->model;
        } else {
            throw new Exceptions\ModelNotFoundException($moduleName, $modelName);
        }
    }
}
