<?php defined('_JEXEC') or die('Restricted access');

class EngineModelDownload extends JModelAdmin
{
	public function getTable($type = 'Download', $prefix = 'EngineTable', $config = array())
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_engine/tables/');
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_' . COMPONENT . '.download.data', array());
		$session = JFactory::getSession();

		if( $session->get('downloadformdatamain') ){
			$data = $session->get('downloadformdatamain');
		}

		return $data;
	}

	public function getForm($data = array(), $loadData = true)
	{
        $form = $this->loadForm('com_engine.download', 'download', array('control' => 'jform', 'load_data' => $loadData));
		
		if($loadData==true&&count($data)>0){
			$form->bind($data);
		}
        
		return $form;
	}
	
	public function save( $data )
	{
        $session = JFactory::getSession();

        if(parent::save(EngineHelper::dataBeforeSave($data))){
            $this->sendMail($data);
            $session->clear("downloadformdatamain");
            return true;
        } else {
            $session->set("downloadformdatamain", $data);

            return false;
        }
	}

    public function sendMail($data)
    {
        $session = JFactory::getSession();

        $replaces = array();
        foreach ($data as $key => $value) {

            if ($data[$key] == null) {
                $replaces[strtoupper($key)] = 'N.V.T.';
            } else {
                $replaces[strtoupper($key)] = $value;
            }
        }

        $session->set('eng.thankyou.replaces', $replaces);
        $params = JComponentHelper::getParams('com_engine');

        $id = $this->state->get($this->getName() . '.id');

        Message4U::send(array(
            'trigger' => 'ENG_DOWNLOAD_INTERNAL',
            'to' => (string)$params->get('email_download'),
            'replaces' => $replaces,
            'crud_id' => $id
        ));

        $mail = array(
            'trigger' => 'ENG_DOWNLOAD_EXTERNAL',
            'user_id' => JFactory::getUser()->id,
            'to' => (string)$data['email'],
            'replaces' => $replaces,
            'crud_id' => $id
        );

        $fileupload = App4U::getParam('fileupload');

        if(strlen($fileupload) && is_file(JPATH_ROOT . $fileupload)){
            $mail['attachment'] = array(
                array('name' => basename($fileupload), 'file' => JPATH_ROOT . $fileupload)
            );
        }

        Message4U::send($mail);
    }
}