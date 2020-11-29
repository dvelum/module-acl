<?php
return [
    'id' => 'dvelum-module-acl',
    'version' => '3.0.1',
    'author' => 'Kirill Yegorov',
    'name' => 'DVelum ACL',
    'configs' => './configs',
    'locales' => './locales',
    'resources' =>'./resources',
    'templates' => './templates',
    'vendor'=>'Dvelum',
    'autoloader'=> [
        './classes'
    ],
    'objects' =>[
        'acl_simple'
    ],
    'post-install'=>''
];