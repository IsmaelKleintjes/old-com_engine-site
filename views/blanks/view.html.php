<?php defined("_JEXEC") or die("Restricted access");

class EngineViewBlanks extends JViewLegacy
{
    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->items = $model->getItems();

        parent::display($tpl);
    }
}