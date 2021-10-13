<?php

/** @var modX $modx */
switch ($modx->event->name) {
    case 'OnMODXInit':
        $modx->getService('mywarehouse', 'MyWarehouse', MODX_CORE_PATH . 'components/mywarehouse/services/');
        break;
}
