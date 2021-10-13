<?php

$config = require 'includes/include.config.php';

//Инициализация MODX
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();

$modx->initialize('mgr');
$modx->setLogLevel(modx::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

//Инициализация билдера
$modx->loadClass('transport.modPackageBuilder', '', false, true);

$builder = new modPackageBuilder($modx);

$builder->createPackage(
    $config['package']['name'],
    $config['package']['version'],
    $config['package']['release']
);

$builder->registerNamespace(
    $config['package']['name_lower'],
    false,
    true, "{core_path}components/{$config['package']['name_lower']}/"
);

//Создание категории
$categoryAttrs = [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
        'Snippets' => [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => $config['update']['snippets'],
            xPDOTransport::UNIQUE_KEY => 'name',
        ],
        'Chunks' => [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => $config['update']['chunks'],
            xPDOTransport::UNIQUE_KEY => 'name',
        ],
        'Plugins' => [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => $config['update']['plugins'],
            xPDOTransport::UNIQUE_KEY => 'name',
        ],
        'PluginEvents' => [
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => $config['update']['events'],
            xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
        ],
    ]
];

/**
 * @var modCategory $category
 */
$category = $modx->newObject('modCategory');
$category->set('category', $config['package']['name']);

$modx->log(modx::LOG_LEVEL_INFO, 'Создана категория: ' . $config['package']['name']);

//Сниппеты
$snippets = include 'data/transport.snippets.php';

$category->addMany($snippets);
$modx->log(modx::LOG_LEVEL_INFO, 'Собрано сниппетов: ' . count($snippets));

//Чанки
$chunks = include 'data/transport.chunks.php';

$category->addMany($chunks);
$modx->log(modx::LOG_LEVEL_INFO, 'Собрано чанков: ' . count($chunks));

//Плагины
$plugins = include 'data/transport.plugins.php';

$category->addMany($plugins);
$modx->log(modx::LOG_LEVEL_INFO, 'Собрано плагинов: ' . count($plugins));

//Системные настройки
$settingsAttrs = [
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
];

$settings = include 'data/transport.settings.php';

foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting, $settingsAttrs);
    $builder->putVehicle($vehicle);
}

$modx->log(modx::LOG_LEVEL_INFO, 'Собрано системных настроек: ' . count($settings));

//События
$eventsAttrs = [
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => $config['update']['events'],
];

$events = include 'data/transport.events.php';

foreach ($events as $event) {
    $vehicle = $builder->createVehicle($event, $eventsAttrs);
    $builder->putVehicle($vehicle);
}

$modx->log(modx::LOG_LEVEL_INFO, 'Собрано событий: ' . count($events));

//Сборка
$vehicle = $builder->createVehicle($category, $categoryAttrs);

$vehicle->resolve('file',
    [
        'source' => COMPONENT_CORE_PATH,
        'target' => "return MODX_CORE_PATH . 'components/';"
    ]
);

$builder->putVehicle($vehicle);

$builder->setPackageAttributes(
    [
        'changelog' => file_get_contents(COMPONENT_DOCS_PATH . 'changelog.txt'),
        'license' => file_get_contents(COMPONENT_DOCS_PATH . 'license.txt'),
        'readme' => file_get_contents(COMPONENT_DOCS_PATH . 'readme.txt'),
    ]
);

if ($builder->pack()) {
    $modx->log(modx::LOG_LEVEL_INFO, 'Пакет готов');
} else {
    $modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке пакета');
}
