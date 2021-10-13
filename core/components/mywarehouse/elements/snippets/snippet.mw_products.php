<?php

/**
 * @var modX $modx
 * @var MyWarehouse $myWarehouse
 */
$myWarehouse = $modx->getService('mywarehouse');

$output = '';

foreach ($myWarehouse->getProducts() as $product) {
    $output .= $modx->getChunk('mwProduct', $product);
}

return $output;
