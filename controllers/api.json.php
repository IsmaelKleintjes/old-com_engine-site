<?php defined('_JEXEC') or die;

class EngineControllerApi extends JControllerForm
{
    public function getModel($name = 'Api', $prefix = 'EngineModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function allow()
    {
        return true;
        $accountId = Input4U::getInt('accountId', 'REQUEST');
        if(Account::get()->id > 0 && Account::get()->id != $accountId) {
             echo new JResponseJson(array());
             exit;
        } else  {
            return true;
        }
    }

    public function getItems()
    {
        $this->checkActiveSession();
        $this->allow();

        $items = array();

        $item = new stdClass();
        $item->id = 1;
        $item->name = 'item1';
        $items[] = $item;

        $item = new stdClass();
        $item->id = 2;
        $item->name = 'item2';
        $items[] = $item;

        echo new JResponseJson($items);
    }

    public function getItem()
    {
        $this->checkActiveSession();
        $this->allow();

        $id = Input4U::getInt('id', 'REQUEST');

        $item = new stdClass();
        $item->id = $id;
        $item->name = 'item' . $id;

        echo new JResponseJson($item);
    }

    public function checkActiveSession()
    {
        $sessionId = Input4U::get('sessionId', 'REQUEST');
        if ($sessionId != JFactory::getSession()->getId() || !JFactory::getUser()->id) {
            echo new JResponseJson(array('activeSession' => 0), 'Session expired', true);
        }
    }

    public function login()
    {
        #$lang = JFactory::getLanguage();
        #$lang->load('', JPATH_SITE, 'en-GB', true);

        $data = Input4U::getArray('jform');
        $credentials = Array('username' => $data['username'], 'password' => $data['password']);
        $response = JFactory::getApplication()->login($credentials);

        if($response) {

            $groups = JFactory::getUser()->groups;

            if(in_array(10, $groups)){ // Mobile
                $response = array(
                    'userId' => JFactory::getUser()->id,
                    'company' => '',
                    'sessionId' => JFactory::getSession()->getId()
                );
            } else {
                JFactory::getApplication()->logout();
                echo new JResponseJson(false, 'You are not authorized to log in', true);
                exit;
            }
        }

        echo new JResponseJson($response);
    }

    public function logout()
    {
        JFactory::getApplication()->logout();
        echo new JResponseJson(false, 'Successfully logged out', true);
        exit;
    }

    public function resetPassword()
    {
        $config = JFactory::getConfig();
        $data = Input4U::getArray('jform', 'REQUEST');

		$data['email'] = JStringPunycode::emailToPunycode($data['email']);

		// Find the user id for the given email address.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName('#__users'))
			->where($db->quoteName('email') . ' = ' . $db->quote($data['email']));

		// Get the user object.
		$db->setQuery($query);

		try
		{
			$userId = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			echo new JResponseJson(JText::sprintf('Error getting the user from the database: %s', $e->getMessage()));
            exit;
		}

		// Check for a user.
		if (empty($userId))
		{
			echo new JResponseJson(JText::_('ongeldig e-mailadres'));
            exit;
		}

		// Get the user object.
		$user = JUser::getInstance($userId);

		// Make sure the user isn't blocked.
		if ($user->block)
		{
			echo new JResponseJson(JText::_('Deze gebruiker is geblokkeerd. Als dit een fout is neem dan contact op met een administrator.'));
            exit;
		}

		// Make sure the user isn't a Super Admin.
		if ($user->authorise('core.admin'))
		{
			echo new JResponseJson(JText::_('A super user can not request a password reminder. Contact another super user or use an alternative method.'));
            exit;
		}

		// Set the confirmation token.
		$token = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
		$hashedToken = JUserHelper::hashPassword($token);

		$user->activation = $hashedToken;

		// Save the user to the database.
		if (!$user->save(true))
		{
			echo new JResponseJson(JText::sprintf('Saving user failed: %s', $user->getError()));
            exit;
		}

		// Assemble the password reset confirmation link.
		$mode = $config->get('force_ssl', 0) == 2 ? 1 : (-1);
		$itemid = '';
		$link = 'index.php?option=com_users&view=reset&layout=confirm&token=' . $token . $itemid;

		// Put together the email template data.
		$data = $user->getProperties();
		$data['link_html'] = JRoute::_($link, true, $mode);
		$data['link_html'] = str_replace('/nl/', '/en/', $data['link_html']);

        $return = Message4U::send(array(
            'id' => 6,
            'translate' => true,
            'lang_id' => 1,
            'to' => $user->email,
            'replaces' => array(
                'RESET_URL' => $data['link_html'],
                'NAME' => $user->name
            )
        ));

		// Check for an error.
		if ($return !== true)
		{
			echo new JResponseJson(JText::_('Email versturen mislukt.'));
            exit;
		}

		return true;
    }
}