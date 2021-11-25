<?php defined('_JEXEC') or die;
jimport('joomla.filter.output');

require_once JPATH_ADMINISTRATOR . '/components/com_engine/helpers/input4u.php';
require_once JPATH_ADMINISTRATOR . '/components/com_engine/helpers/app4u.php';
require_once JPATH_SITE . '/components/com_engine/helpers/route4u.php';

class EngineRouter extends JComponentRouterBase
{
	public function build(&$query)
    {
		$segments = array();
        $db = JFactory::getDbo();

		// Get a menu item based on Itemid or currently active
        $app = JFactory::getApplication();
		$menu = $app->getMenu();

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}

		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_engine')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
		}

		if (isset($query['view']))
		{
			$view = $query['view'];
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}

        /*
         * VIEW switch
         */
        switch( $view )
        {
            case 'thankyou': {
                $parentMenuItem = $menu->getItem($menuItem->parent_id);

                if(isset($query['vacancy_id']) && $query['vacancy_id']>0)
                {
                    $segments[] = '../';
                    $segments[] = '../';
                    $segments[] = '../';

                    $segments[] = self::getVacancyAlias($query['vacancy_id']);

                    $segments[] = $parentMenuItem->alias;

                    $segments[] = $menuItem->alias;

                    unset($query['vacancy_id']);
                }


                break;
            }
        }

        unset($query['view']);
        unset($query['layout']);
        
        foreach($segments as &$segment)
        {
            $segment = strtolower($segment);
        }

		return $segments;
	}

	public function parse(&$segments)
	{
        // Get the active menu item.
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getActive();
        $db = JFactory::getDbo();
        $lang = JFactory::getLanguage();

        if(count($segments)==0) {
            return array();
        }

        $vars = $item->query;

        switch($vars['view'])
        {
            case 'companyvacancies': {
                if(count($segments) > 0){
                    $alias = $segments[0];

                    $id = self::getVacancyIdByAlias($alias);

                    if($id > 0){
                        $vars['id'] = $id;
                        $vars['view'] = 'companyvacancy';
                        $vars['layout'] = 'edit';
                        $vars['Itemid'] = Route4U::getItemid('index.php?option=com_engine&view=companyvacancy&layout=edit');
                    }

                }
            }
            break;
        }

		return $vars;
	}

    public function getMenuItemByAlias($url, $alias, $parentMenuItemUrl)
    {
        $app = JFactory::getApplication();

        $menu = $app->getMenu();
        $parentMenuItem = $menu->getItems( 'link', $parentMenuItemUrl, true );

        $tag = JFactory::getLanguage()->getTag();

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from($db->qn('#__menu'));
        $query->where($db->qn('link') . ' LIKE ' . $db->q('%' . $db->escape((string)$url) . '%'));
        $query->where($db->qn('alias') . ' = ' . $db->q($db->escape((string)$alias)));
        $query->where($db->qn('published') . ' = 1');
        $query->where('(' . $db->qn('language') . ' = ' . $db->q('*') . ' OR ' . $db->qn('language') . ' = ' . $db->q($db->escape((string)$tag)) . ')');
        $query->where($db->qn('parent_id') . ' = ' . $parentMenuItem->id);

        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function getTitle( $id, $table, $column='title' )
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select( $db->qn($column) );
        $query->from("#__eng_" .$table );
        $query->where( "id = " . (int) $id);

        $db->setQuery($query);
        $result = $db->loadResult();

        return JFilterOutput::stringURLUnicodeSlug( $result );
    }
}