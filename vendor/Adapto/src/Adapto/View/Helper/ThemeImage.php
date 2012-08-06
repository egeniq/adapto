<?php 

namespace Adapto\View\Helper;

class ThemeImage extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($image)
    {
        return "adapto_static/".\Adapto\Ui\Theme::getInstance()->getFileLocation("images", $image);
    }
}