<?php

$tmp = [
    'mwProducts' => [
        'file' => 'snippet.mw_products.php',
        'description' => 'Product Cards',
    ]
];

/* ------- Упаковка сниппетов (НЕ ТРОГАТЬ!!!) ------- */
/**
 * @var modX $modx
 * @var array $config
 */

$snippets = [];

foreach ($tmp as $k => $v) {
    /**
     * @var modSnippet $snippet
     */
    $snippet = $modx->newObject('modSnippet');

    //Содержимое сниппета
    $snippetContent = file_get_contents(COMPONENT_CORE_PATH . 'elements/snippets/' . $v['file']);

    //Удаление тега php
    preg_match('#<\?php(.*)#is', $snippetContent, $data);
    $snippetContent = $data[1];

    $snippet->fromArray([
        'id' => 0,
        'name' => $k,
        'description' => $v['description'],
        'snippet' => $snippetContent,
        'static' => $config['static']['snippets'],
        'source' => 1,
        'static_file' => 'core/components/' . $config['package']['name_lower'] . '/elements/snippets/' . $v['file']
    ], '', true, true);

    $snippets[] = $snippet;
}

return $snippets;
