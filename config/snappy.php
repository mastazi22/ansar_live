<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary'  => base_path('vendor/wemersonjanuario/wkhtmltopdf-windows/bin/32bit/wkhtmltopdf.exe'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => base_path('vendor/wemersonjanuario/wkhtmltopdf-windows/bin/32bit/wkhtmltoimage.exe'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);


