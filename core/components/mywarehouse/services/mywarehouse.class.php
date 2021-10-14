<?php

class MyWarehouse
{
    public function getProducts(): array
    {
        return [
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
            [
                'name' => 'Лексус',
                'description' => 'Описание лексуса',
                'price' => '300 000 000 руб.'
            ],
        ];
    }
}
