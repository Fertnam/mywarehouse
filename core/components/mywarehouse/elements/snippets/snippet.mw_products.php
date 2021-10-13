<?php

/**
 * @var modX $modx
 * @var MyWarehouse $myWarehouse
 */

$modx->getService('mywarehouse', 'MyWarehouse', MODX_CORE_PATH . 'components/mywarehouse/services/');
$myWarehouse = $modx->getService('mywarehouse');

$output = '';

foreach ($myWarehouse->getProducts() as $product) {
    $output .= $modx->getChunk('product', $product);
}

return $output;
