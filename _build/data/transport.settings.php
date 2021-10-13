<?php

$tmp = [
    'api' => [
        'xtype' => 'textfield',
        'value' => 'https://www.moysklad.ru/',
    ],
    'token' => [
        'xtype' => 'textfield',
        'value' => '9U8GbwketftCRU1F99fG3Hd2qtwvF9uU',
    ],
];

/* ------- Упаковка системных настроек (НЕ ТРОГАТЬ!!!) ------- */
/**
 * @var modX $modx
 * @var array $config
 */

$settings = [];

foreach ($tmp as $k => $v) {
    /**
     * @var modSystemSetting $setting
     */
    $setting = $modx->newObject('modSystemSetting');

    $setting->fromArray(array_merge(
        [
            'key' => $config['package']['name_lower'] . '_' . $k,
            'namespace' => $config['package']['name_lower'],
            'area' => $config['package']['name_lower'],
        ], $v
    ), '', true, true);

    $settings[] = $setting;
}

return $settings;
