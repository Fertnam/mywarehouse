<?php

$tmp = [
    'mwProduct' => [
        'file' => 'chunk.mw_product.tpl',
        'description' => 'Product Card Chunk',
    ]
];

/* ------- Упаковка чанков (НЕ ТРОГАТЬ!!!) ------- */
/**
 * @var modX $modx
 * @var array $config
 */

$chunks = [];

foreach ($tmp as $k => $v) {
    /**
     * @var modChunk $chunk
     */
    $chunk = $modx->newObject('modChunk');

    $chunk->fromArray([
        'id' => 0,
        'name' => $k,
        'description' => $v['description'],
        'snippet' => file_get_contents(COMPONENT_CORE_PATH . 'elements/chunks/' . $v['file']),
        'static' => $config['static']['chunks'],
        'source' => 1,
        'static_file' => 'core/components/' . $config['package']['name_lower'] . '/elements/chunks/' . $v['file']
    ], '', true, true);

    $chunks[] = $chunk;
}

return $chunks;
