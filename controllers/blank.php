<?php defined( '_JEXEC' ) or die;

class EngineControllerBlank extends JControllerForm
{
    protected $view_list = 'blanks';

    public function getModel($name = 'Blank', $prefix = 'EngineModel', $config=array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
}