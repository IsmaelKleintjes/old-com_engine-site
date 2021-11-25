<?php defined("_JEXEC") or die("Restricted access");

class EngineViewLead extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->form = $this->get("Form");

        Meta4U::setSeoData();

		parent::display($tpl);
	}
}