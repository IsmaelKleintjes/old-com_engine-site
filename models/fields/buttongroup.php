<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Provides radio button inputs
 *
 * @link   http://www.w3.org/TR/html-markup/command.radio.html#command.radio
 * @since  11.1
 */
class JFormFieldButtongroup extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Buttongroup';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  3.5
	 */
	protected $layout = 'engine.form.field.buttongroup';

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		if (empty($this->layout))
		{
			throw new UnexpectedValueException(sprintf('%s has no layout assigned.', $this->name));
		}

		return $this->getRenderer($this->layout)->render($this->getLayoutData());
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extraData = array(
			'options' => $this->getOptions(),
			'value'   => (string) $this->value,
		);

		return array_merge($data, $extraData);
	}

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   3.7.0
     */
    protected function getOptions()
    {
        $fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
        $options   = array();

        foreach ($this->element->xpath('option') as $option)
        {
            // Filter requirements
            if ($requires = explode(',', (string) $option['requires']))
            {
                // Requires multilanguage
                if (in_array('multilanguage', $requires) && !JLanguageMultilang::isEnabled())
                {
                    continue;
                }

                // Requires associations
                if (in_array('associations', $requires) && !JLanguageAssociations::isEnabled())
                {
                    continue;
                }
            }

            $value = (string) $option['value'];
            $text  = trim((string) $option) != '' ? trim((string) $option) : $value;

            $disabled = (string) $option['disabled'];
            $disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');
            $disabled = $disabled || ($this->readonly && $value != $this->value);

            $checked = (string) $option['checked'];
            $checked = ($checked == 'true' || $checked == 'checked' || $checked == '1');

            $selected = (string) $option['selected'];
            $selected = ($selected == 'true' || $selected == 'selected' || $selected == '1');

            $tmp = array(
                'value'    => $value,
                'text'     => JText::alt($text, $fieldname),
                'disable'  => $disabled,
                'class'    => (string) $option['class'],
                'selected' => ($checked || $selected),
                'checked'  => ($checked || $selected),
                'active_class' => (string) $option['active-class']
            );

            // Set some event handler attributes. But really, should be using unobtrusive js.
            $tmp['onclick']  = (string) $option['onclick'];
            $tmp['onchange'] = (string) $option['onchange'];

            // Add the option object to the result set.
            $options[] = (object) $tmp;
        }

        if ($this->element['useglobal'])
        {
            $tmp        = new stdClass;
            $tmp->value = '';
            $tmp->text  = JText::_('JGLOBAL_USE_GLOBAL');
            $component  = JFactory::getApplication()->input->getCmd('option');

            // Get correct component for menu items
            if ($component == 'com_menus')
            {
                $link      = $this->form->getData()->get('link');
                $uri       = new JUri($link);
                $component = $uri->getVar('option', 'com_menus');
            }

            $params = JComponentHelper::getParams($component);
            $value  = $params->get($this->fieldname);

            // Try with global configuration
            if (is_null($value))
            {
                $value = JFactory::getConfig()->get($this->fieldname);
            }

            // Try with menu configuration
            if (is_null($value) && JFactory::getApplication()->input->getCmd('option') == 'com_menus')
            {
                $value = JComponentHelper::getParams('com_menus')->get($this->fieldname);
            }

            if (!is_null($value))
            {
                $value = (string) $value;

                foreach ($options as $option)
                {
                    if ($option->value === $value)
                    {
                        $value = $option->text;

                        break;
                    }
                }

                $tmp->text = JText::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
            }

            array_unshift($options, $tmp);
        }

        reset($options);

        return $options;
    }
}
