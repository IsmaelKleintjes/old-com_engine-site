<?php defined('_JEXEC') or die;

class EngineControllerMedia4U extends JControllerAdmin
{
    public function save()
    {
        Media4U::save();
        exit();
    }

    public function delete($deleteId = 0)
    {
        Media4U::delete( $deleteId );
        exit();
    }

    public function setDefault()
    {
        Media4U::setDefault();
        exit();
    }

    public function saveOrder()
    {
        Media4U::saveOrder();
        exit();
    }

    public function saveImage()
    {
        Media4U::saveImage();
        exit();
    }

    public function saveVideo()
    {
        Media4U::saveVideo();
        exit();
    }

    public function exists()
    {
        Media4U::exists();
        die;
    }
}