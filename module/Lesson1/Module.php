<?php

namespace Lesson1; 

use \Adapto\Menu;

class Module extends \Adapto\Module
{
    public function initMenu(Menu\Menu $menu)
    {
        $lesson1Menu = new Menu\Menu("Lesson1");
        
        $lesson1Menu->addItem(new Menu\Item\Link("http://www.google.com", "Google"));
        $lesson1Menu->addItem(new Menu\Item\Separator());
        $lesson1Menu->addItem(new Menu\Item\Controller("lesson1", "employees"));
        
        $menu->addSubMenu($lesson1Menu);
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                        ),
                ),
        );
    }
}