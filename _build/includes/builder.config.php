<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');

return [
    'package' => [
        'name' => 'MyWarehouse',
        'version' => '0.0.1',
        'release' => 'beta',
    ],
    'static' => [
        'snippets' => true,
        'chunks' => false,
    ],
    'update' => [
        'snippets' => true,
        'chunks' => true,
    ],
];
