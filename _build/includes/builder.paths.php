<?php

/** @noinspection PhpDefineCanBeReplacedWithConstInspection */

$config = require 'builder.config.php';

//Пути MODX
define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');

//Пути компонента
define('COMPONENT_ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/');
define('COMPONENT_BUILD_PATH', COMPONENT_ROOT_PATH . '_build/');
define('COMPONENT_DATA_PATH', COMPONENT_BUILD_PATH . 'data/');
define('COMPONENT_CORE_PATH', COMPONENT_ROOT_PATH . 'core/components/' . $config['package']['name_lower'] . '/');
define('COMPONENT_DOCS_PATH', COMPONENT_CORE_PATH . 'docs/');
