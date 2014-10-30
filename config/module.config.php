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

    'navigation' => array(
        'admin' => array(
            'settings' => array(
                'label' => 'Settings',
                'id' => 'settings-page',
                'route' => 'zfcadmin/settings',
                'order' => 1000,
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

    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        )
    ),

    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'atadmin' => array(
        'logout_route' => ''
    )
);