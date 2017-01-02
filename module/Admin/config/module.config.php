<?php

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'logout',
                            ],
                        ],
                    ],
                    'reset-password' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/reset-password',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action' => 'resetPassword',
                            ],
                        ],
                    ],
                    'set-password' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/set-password',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action' => 'setPassword',
                            ],
                        ],
                    ],
//                    'users' => [
//                        'type' => Segment::class,
//                        'options' => [
//                            'route' => '/users[/:action[/:id]]',
//                            'constraints' => [
//                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                                'id' => '[a-zA-Z0-9_-]*',
//                            ],
//                            'defaults' => [
//                                'controller' => Controller\AdminController::class,
//                                'action' => 'index',
//                            ],
//                        ],
//                    ],
                ]
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\AdminController::class => Controller\Factory\AdminControllerFactory::class,
        ],
    ],
    // The 'access_filter' key is used by the Admin module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            Controller\AdminController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone.
                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
                // Give access to "index", "add", "edit", "view", "changePassword" actions to authorized users only.
                ['actions' => ['index', 'add', 'edit', 'view', 'changePassword'], 'allow' => '@']
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            \Zend\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\AdminManager::class => Service\Factory\AdminManagerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
