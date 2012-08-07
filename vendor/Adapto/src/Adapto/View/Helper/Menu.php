<?php

namespace Adapto\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Menu extends AbstractHelper 
{
    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $_serviceManager;
    
    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }
    
    public function __invoke()
    {        
        $ui = \Adapto\Ui::getInstance();
        
        $menu = new \Adapto\Menu\Menu("Root");
        
        $mm = $this->_serviceManager->getServiceLocator()->get('ModuleManager');
        
        foreach ($mm->getLoadedModules() as $module) {

            if (method_exists($module, 'initMenu')) {
                $module->initMenu($menu);
            }
        }
        
        return $ui->render("menu.phtml", array("menu" => $menu));
    }
}