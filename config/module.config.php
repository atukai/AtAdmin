<?php

return array(
    'router' => array(
        'routes' => array(
            'zfcadmin' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'AtAdmin\Controller\Dashboard',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(

                )
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'AtAdmin\Controller\Dashboard' => 'AtAdmin\Controller\DashboardController',
            'AtAdmin\Controller\DataGrid'  => 'AtAdmin\Controller\DataGridController'
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
