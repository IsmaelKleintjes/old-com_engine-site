<?php defined('_JEXEC') or die;

JLoader::register('MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

abstract class Route4U
{
    public static function getUrl( $url, $component = 'com_engine', $jroute = true)
    {
        $uri = JURI::getInstance($url);
        $view = $uri->getVar('view');
        $layout = $uri->getVar('layout');
        $component = $uri->getVar('option');
        $id = $uri->getVar('id');

        $component = JComponentHelper::getComponent($component);

        $componentId = $component->id;

        if($view)
        {
            $db = JFactory::getDbo();
            $language = JFactory::getLanguage();

            $query = $db->getQuery(true);

            $query->select('id, title, link');
            $query->from('#__menu');

            $query->where("component_id = ".$componentId);
            $query->where("(link LIKE '%view=".$view."' || link LIKE '%view=".$view."&%')");

            // If com_content, also check ID
            if($componentId==22) {
                $query->where("(link LIKE '%id=".$id."' || link LIKE '%id=".$id."&%')");
            }

            if(strlen($layout)){
                $query->where("(link LIKE " . $db->q('%layout='.$layout) . " || link LIKE " . $db->q('%layout='.$layout . '&%') .")");
            } else {
                $query->where("(link NOT LIKE " . $db->q('%layout=%') .")");
            }

            $query->where("(language = '".$language->getTag()."' OR language = '*')");

            $viewLevels = App4U::getAllowedViewlevels();

            if(count($viewLevels)){
                $query->where('(' .$db->qn('access') . ' IN (' . implode(',', $viewLevels) . '))');
            }

            $query->where($db->qn('published') . ' = 1');

            $db->setQuery($query);
            $menu = $db->loadObject();

            if($menu->id>0)
            {
                if($jroute){
                    return JRoute::_( $url . '&Itemid=' . $menu->id, false );
                } else {
                    return $url . '&Itemid=' . $menu->id;
                }
            }
        }

        $uri->setVar('Itemid', Input4U::getInt('Itemd', 'REQUEST'));

        return $uri->toString();
	}

    public function getItemid($url)
    {
        $url = self::getUrl($url, 'com_engine', false);

        $uri = JUri::getInstance($url);

        return $uri->getVar('Itemid');
    }

    public function getMenuItem($url)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__menu');
        $query->where("link = '{$db->escape($url)}'");

        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getLanguage( $shortTag )
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("*");
        $query->from("#__languages");
        $query->where("sef = ".$db->quote($shortTag));

        $db->setQuery($query);
        $item = $db->loadObject();

        return $item;
    }
}
