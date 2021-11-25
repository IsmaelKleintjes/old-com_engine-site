<?php defined('_JEXEC') or die('Restricted access');

class EngineModelThankyou extends JModelAdmin
{
    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }

    public function getText()
    {
        $session = JFactory::getSession();

        $menu = JFactory::getApplication()->getMenu()->getItem(Input4U::getInt('Itemid', 'REQUEST'));

        JModelLegacy::addIncludePath("components/com_content/models/");
        $articleModel = JModelLegacy::getInstance('Article', 'ContentModel');
        $article = $articleModel->getItem($menu->params->get('article_id'));

        $replaces = $session->get('eng.thankyou.replaces');
        $text = $article->introtext;

        if(!empty($replaces)) {
            foreach($replaces as $search => $replace){
                $text = str_replace('{' . $search . '}' , $replace, $text);
                $article->title = str_replace('{' . $search . '}' , $replace, $article->title);
            }
        }


        return array('text' => $text, 'title' => $article->title);
    }
}