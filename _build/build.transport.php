<?php

include 'build.config.php';

//Инициализация modx
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();

$modx->initialize('mgr');
$modx->setLogLevel(modx::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
$modx->loadClass('transport.modPackageBuilder', '', false, true);

//Найстройка сборщика
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');

//Создание и настройка категории
/* @var modCategory $category */
$category = $modx->newObject('modCategory');
$category->set('category', PKG_NAME);

$modx->log(modx::LOG_LEVEL_INFO, 'Создана категория: ' . PKG_NAME);

$categoryTransportAttrs = [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
];

//Сборка сниппетов
$categoryTransportAttrs[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippet'] = [
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'name',
];

$snippets = include COMPONENT_DATA_PATH . 'transport.snippets.php';

if (is_array($snippets)) {
    $category->addMany($snippets);
    $modx->log(modx::LOG_LEVEL_INFO, 'Собрано сниппетов: ' . count($snippets));
} else {
    $modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке сниппетов');
}

$categoryVehicle = $builder->createVehicle($category, $categoryTransportAttrs);

$categoryVehicle->resolve('file',
    [
        'source' => COMPONENT_CORE_PATH,
        'target' => "return MODX_CORE_PATH . 'components/';"
    ]
);

$builder->putVehicle($categoryVehicle);

//Сборка системных настроек
$settingsAttrs = [
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
];

$settings = include COMPONENT_DATA_PATH . 'transport.settings.php';

if (is_array($settings)) {
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting, $settingsAttrs);
        $builder->putVehicle($vehicle);
    }

    $modx->log($modx::LOG_LEVEL_INFO, 'Собрано системных настроек: ' . count($settings));
} else {
    $modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке системных настроек');
}

//Сборка пакета
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
