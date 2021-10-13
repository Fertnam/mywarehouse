<?php /** @noinspection PhpDefineCanBeReplacedWithConstInspection */

$config = [
    'package' => [
        'name' => 'MyWarehouse',
        'version' => '0.0.1',
        'release' => 'beta',
    ],
    'static' => [
        'plugins' => true,
        'snippets' => true,
        'chunks' => true,
    ],
    'update' => [
        'plugins' => true,
        'snippets' => true,
        'chunks' => true,
        'events' => true,
    ],
];

/* ------- Вычисляемые свойства конфига и константы (НЕ ТРОГАТЬ!!!) ------- */
$config['package']['name_lower'] = strtolower($config['package']['name']);

//Пути MODX
define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');

//Пути компонента
define('COMPONENT_ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('COMPONENT_BUILD_PATH', COMPONENT_ROOT_PATH . '_build/');
define('COMPONENT_DATA_PATH', COMPONENT_BUILD_PATH . 'data/');
define('COMPONENT_CORE_PATH', COMPONENT_ROOT_PATH . 'core/components/' . $config['package']['name_lower'] . '/');
define('COMPONENT_DOCS_PATH', COMPONENT_CORE_PATH . 'docs/');

return $config;
