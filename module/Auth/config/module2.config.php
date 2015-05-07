<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController',
        ),
    ),
    
    'router' => array(
        'routes' => array(
            'auth' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/logout',

                    'defaults' => array(
                        'controller' => 'Auth\Controller\Auth',
                        'action'     => 'logout',
                    ),
                ),
            ),
        ),
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label'  => 'Logout',
                'route'  => 'auth',
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'auth' => __DIR__ . '/../view',
        ),
    ),
);