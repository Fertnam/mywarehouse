<?php

$config = [
    'package' => [
        'name' => 'MyWarehouse',
        'version' => '0.0.1',
        'release' => 'beta',
    ],
    'static' => [
        'snippets' => true,
        'chunks' => true,
    ],
    'update' => [
        'snippets' => true,
        'chunks' => true,
    ],
];

/* ------- Вычисляемые свойства конфига (НЕ ТРОГАТЬ!!!) ------- */
$config['package']['name_lower'] = strtolower($config['package']['name']);

return $config;
