<?php defined('_JEXEC') or die;


/**
 * Class JHtmlImagePop
 *
 * @version     1.0
 * @since       21-11-2016
 */
class JHtmlImagePop
{
    private $html;

    /**
     * JHtmlImagePop constructor.
     *
     * @param $images
     * @param $width
     * @param $height
     * @param $type
     *
     * @version     1.0
     * @since       21-11-2016
     */
    public function __construct($images, $width, $height, $type)
    {
        foreach($images as $image)
        {
            if(strlen($image->url)){
                $image->url = JHtmlimage::cache( $image->url, 'cache/com_engine/'. $type .'/'.$image->object_id.'/', 'product', 340, 277);
            }
        }

      $this->create($images);
    }

    /**
     * Creates Image
     *
     * @param $images
     *
     * @version     1.0
     * @since       21-11-2016
     */
    private function create($images)
    {
         $html = "";
         foreach($images AS $i => $image)
         {
             if($i == 0)
             {
                 $html .= "<img id='image".$i ."'  class='img-responsive imgmid active' src='". $image->url ."' alt='". $image->label."'>";
             }
             else
             {
                 $html .= "<img id='image".$i ."'  class='img-responsive imgmid' src='". $image->url ."' alt='". $image->label."'>";
             }
         }


            $html .= "<div class='col-md-12 no-padding'>";
             foreach($images AS $i => $image)
             {
                 $html .= " <img id='$i' class='imgtumb image_link thumbnail' onclick='getImage(this.id)' src=' $image->url' alt='$image->label'>";
             }
            $html .= "</div>";

            $this->html =  $html;
   }

    /**
     * Returns $this->html as string
     *
     * @return mixed
     *
     * @version     1.0
     * @since       21-11-2016
     */
    public function __toString()
    {
        return $this->html;
    }


}
