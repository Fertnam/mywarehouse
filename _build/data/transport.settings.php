<?php

const AREA = PKG_NAME_LOWER . '_main';

$settings = [];

$tmp = [
    'api' => [
        'xtype' => 'textfield',
        'value' => 'https://www.moysklad.ru/',
        'area' => AREA
    ],
    'token' => [
        'xtype' => 'textfield',
        'value' => '9U8GbwketftCRU1F99fG3Hd2qtwvF9uU',
        'area' => AREA
    ],
];

foreach ($tmp as $k => $v) {
    /**
     * @var modSystemSetting $setting
     * @var modX $modx
     */
    $setting = $modx->newObject('modSystemSetting');

    $setting->fromArray(array_merge(
        [
            'key' => PKG_NAME_LOWER . '_' . $k,
            'namespace' => PKG_NAME_LOWER
        ], $v
    ), '', true, true);

    $settings[] = $setting;
}

return $settings;
