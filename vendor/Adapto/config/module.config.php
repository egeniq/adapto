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
                        'adaptoTheme' => function($sm) {
                            $helper = new Adapto\View\Helper\Theme();
                           // $helper->setServiceManager($sm);
                            return $helper;
                        },
                ),
        ),
        'service_manager' => array(
                'factories' => array(
                        'adaptoTranslator' => function($sm) {
                            $i18n = new Adapto\Language($sm);
                            return $i18n;
                        }
                ),
        ),
);