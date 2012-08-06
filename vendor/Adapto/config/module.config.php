<?php
return array(
        'view_helpers' => array(
                'factories' => array(
                        // generic view helpers
                        'adaptoMenu' => function($sm) {
                            $helper = new Adapto\View\Helper\Menu();
                            $helper->setServiceManager($sm);
                            return $helper;
                        },
                ),
        ),
);