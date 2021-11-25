<?php defined( '_JEXEC' ) or die;

class EngineControllerThankyou extends JControllerForm
{
    public function getModel($name = 'Thankyou', $prefix = 'EngineModel', $config=array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
    
}