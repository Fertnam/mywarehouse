<?php

$products = [
    [
        'name' => 'Жигуль',
        'description' => 'Описание жигуля',
        'price' => '100 000 руб.'
    ],
    [
        'name' => 'Прадик',
        'description' => 'Описание прадика',
        'price' => '200 000 000 руб.'
    ],
];

$output = '';

foreach ($products as $product) {
    /**
     * @var modX $modx
     */
    $output .= $modx->getChunk('product', $product);
}

return $output;
