<?php defined('_JEXEC') or die('Restricted access');

class EngineModelNewsletter extends JModelAdmin
{
    public function getTable($type = 'Newsletter', $prefix = 'EngineTable', $config = array())
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_engine/tables/');
        return JTable::getInstance($type, $prefix, $config);
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState('com_' . COMPONENT . '.lead.data', array());
        $session = JFactory::getSession();

        if( $session->get('newsformdatamain') ){
            $data = $session->get('newsformdatamain');
        }

        return $data;
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_engine.newsletter', 'newsletter', array('control' => 'jform', 'load_data' => $loadData));

        if($loadData==true&&count($data)>0){
            $form->bind($data);
        }

        return $form;
    }

    public function save( $data )
    {
        if($this->checkExist($data['email'])) {
            return true;
        }

        $session = JFactory::getSession();

        if(parent::save(EngineHelper::dataBeforeSave($data))){
            $this->apiConnection($data);

            $session->clear("newsformdatamain");
            return true;
        }

        $session->set("newsformdatamain", $data);
        return false;

    }

    public function apiConnection($data)
    {
        switch(App4U::getParam('newsletter_api_integration')){
            default:
                return;
            case 'CP':
                $auth = array('api_key' => App4U::getParam('newsletter_api_cp_key'));
                $listId = App4U::getParam('newsletter_api_cp_list_id');
                $wrap = new CMonitor($listId, $auth);

                // Add subscriber
                $wrap->add(array(
                    'EmailAddress' => $data["email"],
                    'Name' => '',
                    'Resubscribe' => true,
                    'RestartSubscriptionBasedAutoResponders' => false
                ));
                return;
            case 'MC':
                $mailchimp = new Mailchimp4U(App4U::getParam('newsletter_api_mc_key'), App4U::getParam('newsletter_api_mc_list_id'));
                $result = $mailchimp->addSubscriber($data['email']);
                return;
        }
    }

    public function deleteByEmail($email)
    {
        $params = json_decode(JModuleHelper::getModule('mod_eng_newsletter')->params);

        // CampaignMonitor API authentication
        $auth = array('api_key' => $params->api_key);
        $listId = $params->list_id;
        $wrap = new CMonitor($listId, $auth);

        // Delete subscriber
        $wrap->unsubscribe($email);

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->delete("#__eng_newsletter");
        $query->where("email = '{$db->escape($email)}'");

        $db->setQuery($query);
        $db->execute();
    }

    public function checkExist($email)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("*");
        $query->from($db->qn("#__eng_newsletter"));
        //$query->where($db->qn("email") . " = '{$db->q($email)}'");
        $query->where($db->qn('email') . ' = ' . $db->q($email));

        $db->setQuery($query);
        $item = $db->loadObject();

        return $item;
    }
}