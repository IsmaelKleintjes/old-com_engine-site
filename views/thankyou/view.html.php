<?php defined("_JEXEC") or die("Restricted access");

class EngineViewThankyou extends JViewLegacy
{
    public function display($tpl = null)
    {
        $this->texts = $this->getModel()->getText();

        $this->text = $this->texts['text'];
        $this->title = $this->texts['title'];

        parent::display($tpl);
    }
}