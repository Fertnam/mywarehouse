<?php

$tmp = [
    'myWarehouse' => [
        'file' => 'plugin.mywarehouse.php',
        'description' => 'Plugin',
        'events' => [
            'OnMODXInit',
        ]
    ]
];

/* ------- Упаковка плагинов (НЕ ТРОГАТЬ!!!) ------- */
/**
 * @var modX $modx
 * @var array $config
 */

$plugins = [];

foreach ($tmp as $k => $v) {
    /** @var modPlugin $plugin */
    $plugin = $modx->newObject('modPlugin');

    //Содержимое сниппета
    $snippetContent = file_get_contents(COMPONENT_CORE_PATH . 'elements/plugins/' . $v['file']);

    //Удаление тега php
    preg_match('#<\?php(.*)#is', $snippetContent, $data);
    $snippetContent = $data[1];

    $plugin->fromArray([
        'id' => 0,
        'name' => $k,
        'category' => 0,
        'description' => $v['description'],
        'plugincode' => $snippetContent,
        'static' => $config['static']['plugins'],
        'source' => 1,
        'static_file' => 'core/components/' . $config['package']['name_lower'] . '/elements/plugins/' . $v['file'],
    ], '', true, true);

    $events = [];

    if (!empty($v['events'])) {
        foreach ($v['events'] as $k2 => $v2) {
            /** @var modPluginEvent $event */
            $event = $modx->newObject('modPluginEvent');

            $event->fromArray(array(
                'event' => $v2,
                'priority' => 0,
                'propertyset' => 0,
            ), '', true, true);

            $events[] = $event;
        }
    }

    if (!empty($events)) {
        $plugin->addMany($events);
    }

    $plugins[] = $plugin;
}

return $plugins;
