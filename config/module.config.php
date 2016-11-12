<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Qrcode\Controller\Qrcode' => 'Qrcode\Controller\QrcodeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'test' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/test',
                    'defaults' => array(
                        'controller' => 'Qrcode\Controller\Qrcode',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'cache_dir'=> 'public/cache/',
);
