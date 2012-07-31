<?php

class Adapto_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    public function menu()
    {
        $ui = Adapto_Ui::getInstance();
        
        $menu = new Adapto_Menu("Root");
        
        $front = Zend_Controller_Front::getInstance();
        
        foreach ($front->getControllerDirectory() as $module => $path) {
                       
            $moduleFile = $front->getModuleDirectory($module)."/Module.php";
            
            if (file_exists($moduleFile)) {

                require_once($moduleFile);
                
                $classname = ucfirst($module)."_Module";
                           
                $module = new $classname();
                
                $module->initMenu($menu);
            }
            
        }
        
        return $ui->render("menu.phtml", array("menu" => $menu));
    }
}