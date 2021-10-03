<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

//Основные параметры
define('PKG_NAME', 'MyWarehouse');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '0.0.1');
define('PKG_RELEASE', 'beta');

//Пути компонента
define('COMPONENT_ROOT_PATH', dirname(dirname(__FILE__)) . '/');
define('COMPONENT_BUILD_PATH', COMPONENT_ROOT_PATH . '_build/');
define('COMPONENT_DATA_PATH', COMPONENT_ROOT_PATH . '_build/data/');
define('COMPONENT_CORE_PATH', COMPONENT_ROOT_PATH . 'core/components/' . PKG_NAME_LOWER . '/');
define('COMPONENT_ASSETS_PATH', COMPONENT_ROOT_PATH . 'assets/components/' . PKG_NAME_LOWER . '/');
define('COMPONENT_DOCS_PATH', COMPONENT_CORE_PATH . 'docs/');

//Пути MODX
define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');

//URL's
define('MODX_BASE_URL', '/');
define('MODX_CORE_URL', MODX_BASE_URL . 'core/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');

//Опции сборки пакета
define('BUILD_CHUNK_UPDATE', true);
define('BUILD_SNIPPET_UPDATE', true);

//Подключение чанков и сниппетов из статических файлов
define('BUILD_CHUNK_STATIC', false);
define('BUILD_SNIPPET_STATIC', true);
