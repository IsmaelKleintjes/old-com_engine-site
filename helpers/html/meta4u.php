<?php

/**
 * Class Meta
 *
 * @version     1.0
 * @since       21-11-2016
 */
class Meta4U
{
    /**
     * Sets the right title
     *
     * @param $item
     * @param string $titleColumn
     * @param string $introColumn
     * @param bool $currentUrl
     *
     * @version     1.0
     * @since       21-11-2016
     */
    public static function setSeoData($item = false, $titleColumn = 'title', $introColumn = 'intro', $currentUrl = true)
    {
        $doc = JFactory::getDocument();
        $menu = JFactory::getApplication()->getMenu()->getActive();

        if(!$menu) return;

        $checkMenu = ($menu->query['view'] == Input4U::get('view', 'REQUEST'));

        $title = $checkMenu && strlen($menu->params->get('page_title')) ? $menu->params->get('page_title') : (property_exists($item, $titleColumn) && strlen($item->$titleColumn) ? $item->$titleColumn : $menu->title);
        $metaTitle = $checkMenu && strlen($menu->params->get('page_title')) ? $menu->params->get('page_title') : (strlen($item->meta_title) ? $item->meta_title : (strlen($item->$titleColumn) ? $item->$titleColumn : $menu->title));
        $description =  $checkMenu && strlen($menu->params->get('menu-meta_description')) ? $menu->params->get('menu-meta_description') : (property_exists($item, $titleColumn) && strlen($item->$introColumn) ? $item->$introColumn : Input4U::getCfg('MetaDesc'));
        $metaDescription = $checkMenu && strlen($menu->params->get('menu-meta_description')) ? $menu->params->get('menu-meta_description') : $item->meta_description;
        $metaDescription = (strlen($metaDescription) ? $metaDescription : (strlen($item->$introColumn) ? $item->$introColumn : Input4U::getCfg('MetaDesc')));
        $metaKeywords = $checkMenu && strlen($menu->params->get('menu-meta_keywords')) ? $menu->params->get('menu-meta_keywords') : (strlen($item->keywords) ? $item->keywords : Input4U::getCfg('MetaKeys'));
        $robots = $checkMenu && strlen($menu->params->get('robots')) ? $menu->params->get('robots') : Input4U::getCfg('robots');
        $ogUrl = $currentUrl ? JUri::getInstance() : JUri::getInstance(JUri::base());
        $ogUrl->setVar('v', time());

        if(strlen($title)) $doc->setTitle(self::title($title)); // <title></title>

        $doc->setMetaData('title', $metaTitle); // <meta name=""/>
        $doc->setMetaData('description', self::introtext($metaDescription)); // <meta name=""/>
        $doc->setMetaData('keywords', $metaKeywords); // <meta name=""/>
        $doc->setMetaData('author', Input4U::getCfg('sitename')); // <meta name=""/>
        $doc->setMetadata('robots', $robots);

        $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:title" content="' . $title . '" />');
        $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:site_name" content="' . Input4U::getCfg('sitename') . '" />');
        $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:url" content="' . htmlspecialchars($ogUrl->__toString(), ENT_COMPAT, 'UTF-8') . '" />');
        $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:description" content="' . self::introtext($description) . '" />');
        $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:type" content="article" />');

        $doc->setMetaData('twitter:card', 'summary_large_image');
        $doc->setMetaData('twitter:title', $title);
        $doc->setMetaData('twitter:description', self::introtext($description));

        
        if(strlen($item->default_image)){
            $pathinfo = pathinfo($item->default_image);
            $mimeType = Media4U::getMimeType($item->default_image);

            // Twitter optimal image
            $image = JHtmlImage::cache($item->default_image, $pathinfo['dirname'] . '/', null, 480, 480);
            $dimensions = getimagesize(str_replace(JUri::base(), '', $image));

            $doc->setMetaData('twitter:image', $image);
            $doc->setMetaData('twitter:image:width', $dimensions[0]);
            $doc->setMetaData('twitter:image:height', $dimensions[1]);

            // LinkedIn optimal image
            $image = JHtmlImage::cache($item->default_image, $pathinfo['dirname'] . '/', null, 180, 110);
            $dimensions = getimagesize(str_replace(JUri::base(), '', $image));

            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image" content="' . $image . '" />');
            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image:type" content="' . $mimeType . '" />');
            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image:width" content="' . $dimensions[0] . '" />');
            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image:height" content="' . $dimensions[1] . '" />');

            // Facebook optimal image
            $image = JHtmlImage::cache($item->default_image, $pathinfo['dirname'] . '/', null, 1200, 630);
            $dimensions = getimagesize(str_replace(JUri::base(), '', $image));

            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image" content="' . $image . '" />');
            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image:type" content="' . $mimeType . '" />');
            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image:width" content="' . $dimensions[0] . '" />');
            $doc->addCustomTag('<meta prefix="og: http://ogp.me/ns#" property="og:image:height" content="' . $dimensions[1] . '" />');
        }
    }

    /**
     * Description comes later
     *
     * @param $text
     * @return string
     *
     * @version     1.0
     * @since       21-11-2016
     */
    public static function title($text)
    {
        switch(Input4U::getCfg('sitename_pagetitles'))
        {
            case 0: {
                return $text;
            }
            case 2: {
                return JText::sprintf('JPAGETITLE', $text, Input4U::getCfg('sitename'));
            }
            case 1: {
                return JText::sprintf('JPAGETITLE', Input4U::getCfg('sitename'), $text);
            }
        }
    }

    /**
     * Description comes later
     *
     * @param $text
     *
     * @return mixed
     *
     * @version     1.0
     * @since       21-11-2016
     */
    public static function introtext($text)
    {
        return JHtml::_('string.truncate', strip_tags($text), 130, true, false);
    }
}