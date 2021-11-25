<?php defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldMultiselect extends JFormFieldList
{
    public $type = 'Multiselect';

    protected function getOptions()
    {
        $options = array();

        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        $query = (string) $this->element['query'];

        $db = JFactory::getDbo();

        $db->setQuery(str_replace("{ID}", Input4U::getInt("id"), $query));
        $items = $db->loadObjectList();
        if (!empty($items))
        {
            foreach ($items as $item)
            {
                if ($translate == true)
                {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                }
                else
                {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        $options = array("options" => array_merge(parent::getOptions(), $options), "items" => $items);

        return $options;
    }

    public function getActive()
    {
        $db = JFactory::getDbo();

        $db->setQuery(str_replace("{ID}", Input4U::getInt("id", 'GET'), $this->element['active_query']))->execute();

        $actives = $db->loadObjectList();

        $active_field = (string) $this->element['active_field'];

        $array = array();
        foreach($actives as $active){
            $array[$active->$active_field] = $active->$active_field;
        }
        return $array;
    }

    protected function getInput()
    {
        $html = array();
        $attr = '';
        $values = $this->getActive();
        $options = $this->getOptions();

        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];

        $html = "<select data-placeholder=' - Selecteer - ' class='chosen " . $this->element['class'] . "' id='".$this->id."' name='$this->name[]' multiple>";
        foreach($options["options"] as $i => $option){
            $selected = "";
            if(isset($values[$option->value])){
                $selected = " selected";
            }
            if($category = (string)$this->element['category']){
                if(!empty($options["items"][$i]->$category)){
                    $cat = " cat='".$options["items"][$i]->$category."'";
                }
            }
            $html .= "<option".$cat." value='".$option->value."'$selected>".$option->text."</option>";
        }
        $html .= "</select>";

        return $html;
    }
}
