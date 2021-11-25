<?php defined('_JEXEC') or die;

jimport('PHPImageWorkshop.ImageWorkshop');
use PHPImageWorkshop\ImageWorkshop; // Use the namespace of ImageWorkshop


/**
 * Class JHtmlImage
 *
 * @version     1.0
 * @since       21-11-2016
 */
class JHtmlImage
{
    /**
     * Gets All
     *
     * @param $id
     * @param $type
     * @param $width
     * @param $height
     * @param int $scale
     * @param int $limit
     *
     * @return mixed
     *
     * @version     1.0
     * @since       21-11-2016
     */
    public function getAll($id, $type, $width, $height, $limit = 0)
	{
		$type = (string) $type;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$int = 1;

		$query->select($db->quoteName(array('id', 'url', 'alt_tag')));
		$query->from($db->quoteName('#__eng_media'));
		$query->where($db->quoteName("object_id") . " = " . (int) $id );
		$query->where($db->quoteName("table") . " = " . $db->quote($type));
        $query->order($db->quoteName('ordering').' ASC');

        if($limit > 0){
            $db->setQuery($query, 0, $limit);
        } else {
            $db->setQuery($query);
        }

		$items = $db->loadObjectList();

		$i = 0;
		foreach($items as $item)
		{
			$i++;
			if(file_exists( JPATH_ROOT . '/' . $item->url ))
			{
			    $item->image_url = self::cache( $item->url, 'cache/com_engine/'.$type.'/'.$id.'/', $type, $width, $height, $crop );
                $item->image = "<img class='uploadImage' src='" . $item->image_url . "' alt='' />";
			}
		}

		return $items;
	}

    public function getDefault($id, $type, $width = 0, $height = 0)
    {
        $type = (string) $type;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $int = 1;

        $query->select($db->quoteName(array('id', 'url', 'alt_tag')));
        $query->from($db->quoteName('#__eng_media'));
        $query->where($db->qn('default') . ' = 1');
        $query->where($db->quoteName("object_id") . " = " . (int) $id );
        $query->where($db->quoteName("table") . " = " . $db->quote($type));

        $db->setQuery($query, 0, 1);

        $item = $db->loadObject();

        if($item){
            if(file_exists( JPATH_ROOT . '/' . $item->url ))
            {
                if($width > 0 && $height > 0){
                    $item->image_url = self::cache( $item->url, 'cache/com_engine/'.$type.'/'.$id.'/', $type, $width, $height, $crop );
                    $item->image = "<img class='uploadImage' src='" . $item->image_url . "' alt='' />";
                }
            }

        }

        return $item;
    }

    public function cache( $url, $folder, $type, $width, $height, $scale=1 )
    {
        ini_set('memory_limit', '500M');

        $folder = str_replace('cache/', 'media/', $folder);

        $cacheFolder = $folder;
        $path_parts = pathinfo( $url );

        $cacheFilename = $path_parts['filename']."_".$width."x".$height."." . $path_parts['extension'];

        if((!file_exists( JPATH_ROOT .'/'. $cacheFolder . $cacheFilename ) || Input4U::get('cacheeverything', 'REQUEST') == true) && file_exists(JPATH_ROOT .'/'. $url))
        {
            $originalSize = getimagesize(JPATH_ROOT . '/' . $url);

            try{

                $thumbnail = ImageWorkshop::initFromPath( JPATH_ROOT .'/'. $url);
            } catch(Exception $ex){
                return '';
            }

            $originalWidth = $originalSize[0];
            $originalHeight = $originalSize[1];

            if($originalWidth > $width){
                if($originalWidth == $originalHeight){
                    $height = $width;

                    $thumbnail->resizeInPixel( $width, $height, false);
                } else {
                    if($originalWidth > $originalHeight){
                        $thumbnail->resizeByLargestSideInPixel( $width, true);
                    } else {
                        $thumbnail->resizeByNarrowSideInPixel( $width, true);
                    }
                }
            }

            $newSizes = self::getNewSize($originalWidth, $originalHeight, $width, $height);

            $thumbnail->resizeInPixel( $newSizes[0], $newSizes[1], false);

            $derp = $thumbnail->save( JPATH_ROOT . '/' . $cacheFolder, $cacheFilename );
        }

        return JURI::root(false)  . ltrim($cacheFolder . $cacheFilename, '/');
    }

    public static function getNewSize($originalWidth, $originalHeight, $toWidth, $toHeight)
    {
        if($originalWidth == $originalHeight){
            if($toWidth > $toHeight || ($toWidth == $toHeight)){
                $newWidth = $toHeight;
                $newHeight = $toHeight;
            } else {
                $newWidth = $toWidth;
                $newHeight = $toWidth;
            }
        } elseif($originalWidth > $originalHeight){
            $newHeight = (($originalHeight * $toWidth) / $originalWidth);
            $newWidth = $toWidth;

            if($newHeight > $toHeight){
                $newHeight = $toHeight;
                $newWidth = (($originalWidth * $toHeight) / $originalHeight);
            }
        } else {
            $newWidth = (($originalWidth * $toHeight) / $originalHeight);

            $newHeight = $toHeight;

            if($newWidth > $toWidth){
                $newWidth = $toWidth;
                $newHeight = (($originalHeight * $toWidth) / $originalWidth);
            }
        }

        return array($newWidth, $newHeight);
    }
}