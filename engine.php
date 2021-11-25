<?php defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

setlocale(LC_TIME, array('Dutch_Netherlands', 'Dutch', 'nl_NL', 'nl', 'nl_NL.ISO8859-1', 'nld_NLD'));

#	DEFINES
define('COMPONENT', 'engine');
define('DS', '/');

#	INCLUDES
Jimport('joomla.application.component.controllerlegacy');
Jimport('joomla.filesystem.file');
jimport( 'joomla.filesystem.folder' );

require_once(JPATH_SITE . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'route4u.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'mailchimp4u.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'html' . DS . 'image4u.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'html' . DS . 'imagePop4u.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'html' . DS . 'meta4u.php');


require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'app4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'engine.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'input4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'message4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'file4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'media4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'price4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'pdf4u.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'campaignmonitor.php');
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'language4u.php');


require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'html' . DS . 'overview4u.php' );
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_engine' . DS . 'helpers' . DS . 'html' . DS . 'detail4u.php');

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

#	FRAMEWORK
JHtml::_('bootstrap.framework');

$controller = JControllerLegacy::getInstance(COMPONENT);
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
