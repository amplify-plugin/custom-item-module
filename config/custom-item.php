<?php

return [
    'configurations' => [
        'version' => '9.3',
        'adapter' => \Amplify\System\CustomItem\Services\Adapters\RhsAdapterService::class,
        'customer_id_field' => 'customer_code',
        'guest_default' => null,
        'delivery_options' => [],
        'url' => 'https://www.rhsparts.com/api',
        'username' => 'SequoiaWEB',
        'password' => 'rH@rd#Sply',
        'enabled' => true,
        'multiple_warehouse' => false,
    ],
];
