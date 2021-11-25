<?php defined('_JEXEC') or die;

class EngineControllerMessagelog extends JControllerLegacy
{
    public function getModel($name = 'Messagelog', $prefix = 'EngineModel', $config=array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function setOpened()
    {
        $hash = Input4U::get('hash', 'GET');

        if(!empty($hash)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select("*");
            $query->from($db->qn("#__eng_message_log"));
            $query->where($db->qn("hash") . " = " . $db->q($hash));

            $db->setQuery($query);
            $item = $db->loadObject();

            if($item->id > 0 && $item->opened == 0) {
                $uMailLog = new stdClass();
                $uMailLog->id = $item->id;
                $uMailLog->opened = 1;
                $uMailLog->opened_on = date("Y-m-d H:i:s");

                JFactory::getDbo()->updateObject('#__eng_message_log', $uMailLog, 'id');
            }
        }

        exit;
    }
}