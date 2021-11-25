<?php defined( '_JEXEC' ) or die;

class EngineControllerDownload extends JControllerForm
{	
	public function getModel($name = 'Download', $prefix = 'EngineModel', $config=array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
	
	public function save()
    {
        if(parent::save()){
            return $this->setRedirect(JRoute::_('index.php?Itemid=' . App4U::getParam('thankyou_download_menuitem'), false), false, false);
        }

        return $this->setRedirect(JRoute::_($_SERVER["HTTP_REFERER"]));
	}
	
	public function allowSave()
	{
		return true;
	}
}