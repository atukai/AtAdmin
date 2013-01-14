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
                    'system' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route'    => '/system',
                            'defaults' => array(
                                'controller' => 'AtAdmin\Controller\System',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'modules' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route'    => '/modules',
                                    'defaults' => array(
                                        'controller' => 'AtAdmin\Controller\System',
                                        'action'     => 'modules',
                                    ),
                                )
                            ),
                            'settings' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route'    => '/settings',
                                    'defaults' => array(
                                        'controller' => 'AtAdmin\Controller\System',
                                        'action'     => 'settings',
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
            'AtAdmin\Controller\System'    => 'AtAdmin\Controller\SystemController',
        ),
    ),

    'navigation' => array(
        'admin' => array(
            'system' => array(
                'label' => 'System',
                'id' => 'system-page',
                'route' => 'zfcadmin/system',
                'order' => 100,
                'pages' => array(
                   /* 'modules' => array(
                        'label' => 'Modules',
                        'route' => 'zfcadmin/system/modules'
                    ),*/
                    'settings' => array(
                        'label' => 'Settings',
                        'route' => 'zfcadmin/system/settings'
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
);