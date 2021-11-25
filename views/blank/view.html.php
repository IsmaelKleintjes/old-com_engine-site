<?php defined("_JEXEC") or die("Restricted access");

class EngineViewBlank extends JViewLegacy
{
	public function display($tpl = null) 
	{
		$this->form = $this->get("Form");
        $this->item = $this->get("Item");

        $this->setMetaData( $this->item );

		parent::display($tpl);
	}

    public function setMetaData( $item )
    {
        $doc = JFactory::getDocument();

        $desc = strip_tags($item->introtext);
        $desc = str_replace(array("\n", "\r"), '',$desc);
        $desc = trim($desc);

        $doc->setMetaData('description',$desc);
        $doc->setTitle($item->title . ' | Assortment');

        $doc->addCustomTag( '<'.'meta property="og:title" content="' . $item->title . '" />' );
        $doc->addCustomTag( '<'.'meta property="og:type" content="article" />' );
        $doc->addCustomTag( '<'.'meta property="og:url" content="' . JURI::base() . substr($_SERVER['REQUEST_URI'],1) . '" />' );
        $doc->addCustomTag( '<'.'meta property="og:image" content="' . $item->title . '" />' );
        $doc->addCustomTag( '<'.'meta property="og:description" content="' . $desc . '" />' );
    }
}