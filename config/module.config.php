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
                    'settings' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route'    => '/system',
                            'defaults' => array(
                                'controller' => 'AtAdmin\Controller\Settings',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'general' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route'    => '/general',
                                    'defaults' => array(
                                        'controller' => 'AtAdmin\Controller\Settings',
                                        'action'     => 'general',
                                    ),
                                )
                            ),
                        )
                    ),
                )
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'AtAdmin\Controller\Dashboard' => 'AtAdmin\Controller\DashboardController',
            'AtAdmin\Controller\Settings'    => 'AtAdmin\Controller\SettingsController',
        ),
    ),

    'navigation' => array(
        'admin' => array(
            'settings' => array(
                'label' => 'Settings',
                'id' => 'settings-page',
                'route' => 'zfcadmin/settings',
                'order' => 100,
                'pages' => array(
                    'general' => array(
                        'label' => 'General',
                        'route' => 'zfcadmin/settings/general'
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'atadmin' => array(
        'logout_route' => ''
    )
);