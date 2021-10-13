<?php

$tmp = [
    'OnMODXInit'
];

/* ------- Упаковка событий (НЕ ТРОГАТЬ!!!) ------- */
/**
 * @var modX $modx
 * @var array $config
 */

$events = [];

foreach ($tmp as $k => $v) {
    /** @var modEvent $event */
    $event = $modx->newObject('modEvent');

    $event->fromArray([
        'name' => $v,
        'service' => 6,
        'groupname' => $config['package']['name'],
    ], '', true, true);

    $events[] = $event;
}

return $events;
