<?php defined('_JEXEC') or die('Restricted access');

class EngineModelLead extends JModelAdmin
{
	public function getTable($type = 'Lead', $prefix = 'EngineTable', $config = array())
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_engine/tables/');
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_' . COMPONENT . '.lead.data', array());
		$session = JFactory::getSession();

		if( $session->get('leadformdatamain') ){
			$data = $session->get('leadformdatamain');
		}

		return $data;
	}

	public function getForm($data = array(), $loadData = true)
	{
        $form = $this->loadForm('com_engine.lead', 'lead', array('control' => 'jform', 'load_data' => $loadData));
		
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
            $session->clear("leadformdatamain");
            return true;
        } else {
            $session->set("leadformdatamain", $data);

            return false;
        }
	}

    public function sendMail($data)
    {
        $session = JFactory::getSession();
        $data['message'] = nl2br($data['message']);

        $replaces = array();
        foreach ($data as $key => $value) {

            if ($data[$key] == null) {
                $replaces[strtoupper($key)] = 'N.V.T.';
            } else {
                $replaces[strtoupper($key)] = $value;
            }
        }

        $id = $this->state->get($this->getName() . '.id');

        $session->set('eng.thankyou.replaces', $replaces);
        $params = JComponentHelper::getParams('com_engine');
        Message4U::send(array(
            'trigger' => 'ENG_LEAD_INTERNAL',
            'to' => (string)$params->get('email'),
            'replaces' => $replaces,
            'crud_id' => $id
        ));
        Message4U::send(array(
            'trigger' => 'ENG_LEAD_EXTERNAL',
            'user_id' => JFactory::getUser()->id,
            'to' => (string)$data['email'],
            'replaces' => $replaces,
            'crud_id' => $id
        ));
    }
}