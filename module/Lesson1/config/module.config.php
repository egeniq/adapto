<?php

return array(
        
    'controllers' => array(
        'invokables' => array(
            'Lesson1\Controller\Employees' => 'Lesson1\Controller\EmployeesController'
        )
    ),
    'router' => array(
        'routes' => array(
            'lesson1' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/lesson1',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Lesson1\Controller',
                        'controller'    => 'EmployeesController',
                        'action'        => 'list',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:controller[/:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Lesson1\Controller',
                            ),
                        ),
                            
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_patterns' => array(
            array(
                'type'     => 'array',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => 'module/Application/view/layout/layout.phtml',
            'error/404'               => 'module/Application/view/error/404.phtml',
            'error/index'             => 'module/Application/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);