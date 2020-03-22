<?php
return array(
//    'pdf' => array(
//        'enabled' => true,
//        'binary' => '/usr/bin/wkhtmltopdf',
//        'timeout' => false,
//        'options' => array(
//            'encoding' => 'utf-8'
//        ),
//        'env' => array(),
//    ),
//    'image' => array(
//        'enabled' => true,
//        'binary' => '/usr/bin/wkhtmltoimage',
//        'timeout' => false,
//        'options' => array(),
//        'env' => array(),
//    ),

    'pdf' => array(
        'enabled' => true,
        'binary' => base_path('vendor\wemersonjanuario\wkhtmltopdf-windows\bin\32bit\wkhtmltopdf'),
        'timeout' => false,
        'options' => array(
            'encoding' => 'utf-8'
        ),
        'env' => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => base_path('vendor\wemersonjanuario\wkhtmltopdf-windows\bin\32bit\wkhtmltoimage'),
        'timeout' => false,
        'options' => array(),
        'env' => array(),
    ),
);
