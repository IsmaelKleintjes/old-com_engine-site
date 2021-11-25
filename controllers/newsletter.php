<?php defined( '_JEXEC' ) or die;

class EngineControllerNewsletter extends JControllerForm
{	
	public function getModel($name = 'Newsletter', $prefix = 'EngineModel', $config=array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
	
	public function save()
    {
        $session = JFactory::getSession();
        $session->set("newsletterformdatamain", Input4U::getArray('jform'));

        if(parent::save()){
            $session->clear("newsletterformdatamain");
            return $this->setRedirect($_SERVER["HTTP_REFERER"], 'Je bent succesvol ingeschreven voor onze nieuwsbrief!', 'success');
        } else {
            return $this->setRedirect($_SERVER["HTTP_REFERER"]);
        }
	}
	
	public function allowSave()
	{
		return true;
	}
}