<?php defined('_JEXEC') or die('Restricted access');

class EngineModelFeature extends JModelAdmin
{
    public function getTable($type = 'Feature', $prefix = 'EngineTable', $config = array())
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_engine/tables/');
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }

    public function getIdsByTitle($title)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("id");
        $query->from($db->qn("#__eng_feature"));
        $query->where($db->qn("title") . " LIKE " . $db->q('%' . $title . '%'));
        $db->setQuery($query);
        $items = $db->loadColumn();

        return $items;
    }
}