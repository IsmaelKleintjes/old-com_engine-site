<?php defined("_JEXEC") or die("Restricted access");

class EngineModelBlanks extends JModelList
{
    public function getListQuery()
    {
        $query = parent::getListQuery();

        $query->select( "*" );
        $query->from( "#__engine_blank" );
        $query->where( "published = 1" );

        return $query;
    }
}