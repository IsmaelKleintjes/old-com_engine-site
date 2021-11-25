<?php defined('_JEXEC') or die('Restricted access');

class EngineModelBlank extends JModelAdmin
{
    public function getTable($type = 'Blank', $prefix = 'EngineTable', $config = array())
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_engine/tables/');
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        return $this->loadForm('com_' . COMPONENT . '.blank', 'blank', array('control' => 'jform', 'load_data' => $loadData));
    }
}