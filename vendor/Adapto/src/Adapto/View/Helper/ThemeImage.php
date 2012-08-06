<?php 

class Adapto_View_Helper_ThemeImage extends Zend_View_Helper_Abstract
{
    public function themeImage($image)
    {
        return "adapto_static/".Adapto_Ui_Theme::getInstance()->getFileLocation("images", $image);
    }
}