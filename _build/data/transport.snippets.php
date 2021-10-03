<?php

$snippets = [];

$tmp = [
    'hellSnippet' => [
        'file' => 'hell.snippet.php',
        'description' => 'HEEEEELL',
    ]
];

foreach ($tmp as $k => $v) {
    /**
     * @var modSnippet $snippet
     * @var modX $modx
     */
    $snippet = $modx->newObject('modSnippet');

    //Содержимое сниппета
    $snippetContent = file_get_contents(COMPONENT_CORE_PATH . 'elements/snippets/' . $v['file']);

    //Удаление тега php
    preg_match('#\<\?php(.*)#is', $snippetContent, $data);
    $snippetContent = $data[1];

    $snippet->fromArray([
        'id' => 0,
        'name' => $k,
        'description' => $v['description'],
        'snippet' => $snippetContent,
        'static' => BUILD_SNIPPET_STATIC,
        'source' => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/' . $v['file']
    ], '', true, true);

    $snippets[] = $snippet;
}

return $snippets;
