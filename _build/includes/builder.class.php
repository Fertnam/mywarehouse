<?php

require_once 'builder.paths.php';

/**
 * Class Builder
 */
class Builder
{
    private modX $modx;
    private modPackageBuilder $builder;
    private modCategory $category;
    private array $categoryAttrs;
    private array $config;

    private function initConfig(): void
    {
        $this->config = require 'builder.config.php';
    }

    private function initModx(): void
    {
        require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

        $this->modx = new modX();

        $this->modx->initialize('mgr');
        $this->modx->setLogLevel(modx::LOG_LEVEL_INFO);
        $this->modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
    }

    private function initBuilder(): void
    {
        $this->modx->loadClass('transport.modPackageBuilder', '', false, true);

        $this->builder = new modPackageBuilder($this->modx);

        $this->builder->createPackage(
            $this->config['package']['name'],
            $this->config['package']['version'],
            $this->config['package']['release']
        );

        $this->builder->registerNamespace(
            $this->config['package']['name_lower'],
            false,
            true, "{core_path}components/{$this->config['package']['name_lower']}/"
        );
    }

    private function initCategory(): void
    {
        $this->categoryAttrs = [
            xPDOTransport::UNIQUE_KEY => 'category',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
        ];

        /**
         * @var modCategory $category
         */
        $category = $this->modx->newObject('modCategory');
        $category->set('category', $this->config['package']['name']);

        $this->category = $category;

        $this->modx->log(modx::LOG_LEVEL_INFO, 'Создана категория: ' . $this->config['package']['name']);
    }

    private function initSnippets(): void
    {
        $this->categoryAttrs[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => $this->config['update']['snippets'],
            xPDOTransport::UNIQUE_KEY => 'name',
        ];

        $snippets = [];

        $tmp = include COMPONENT_DATA_PATH . 'transport.snippets.php';

        foreach ($tmp as $k => $v) {
            /**
             * @var modSnippet $snippet
             */
            $snippet = $this->modx->newObject('modSnippet');

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
                'static' => $this->config['static']['snippets'],
                'source' => 1,
                'static_file' => 'core/components/' . $this->config['package']['name_lower'] . '/elements/snippets/' . $v['file']
            ], '', true, true);

            $snippets[] = $snippet;
        }

        $this->category->addMany($snippets);
        $this->modx->log(modx::LOG_LEVEL_INFO, 'Собрано сниппетов: ' . count($snippets));
    }

    private function initChunks(): void
    {
        $this->categoryAttrs[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => $this->config['update']['chunks'],
            xPDOTransport::UNIQUE_KEY => 'name',
        ];

        $chunks = [];

        $tmp = include COMPONENT_DATA_PATH . 'transport.chunks.php';

        foreach ($tmp as $k => $v) {
            /**
             * @var modChunk $chunk
             */
            $chunk = $this->modx->newObject('modChunk');

            $chunk->fromArray([
                'id' => 0,
                'name' => $k,
                'description' => $v['description'],
                'snippet' => file_get_contents(COMPONENT_CORE_PATH . 'elements/chunks/' . $v['file']),
                'static' => $this->config['static']['chunks'],
                'source' => 1,
                'static_file' => 'core/components/' . $this->config['package']['name_lower'] . '/elements/chunks/' . $v['file']
            ], '', true, true);

            $chunks[] = $chunk;
        }

        $this->category->addMany($chunks);
        $this->modx->log(modx::LOG_LEVEL_INFO, 'Собрано чанков: ' . count($chunks));
    }

    private function initSystemSettings(): void
    {
        $attrs = [
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        ];

        $settings = [];

        $tmp = include COMPONENT_DATA_PATH . 'transport.settings.php';

        foreach ($tmp as $k => $v) {
            /**
             * @var modSystemSetting $setting
             */
            $setting = $this->modx->newObject('modSystemSetting');

            $setting->fromArray(array_merge(
                [
                    'key' => $this->config['package']['name_lower'] . '_' . $k,
                    'namespace' => $this->config['package']['name_lower'],
                    'area' => $this->config['package']['name_lower'],
                ], $v
            ), '', true, true);

            $settings[] = $setting;
        }

        foreach ($settings as $setting) {
            $vehicle = $this->builder->createVehicle($setting, $attrs);
            $this->builder->putVehicle($vehicle);
        }

        $this->modx->log(modx::LOG_LEVEL_INFO, 'Собрано системных настроек: ' . count($settings));
    }

    private function pack(): void
    {
        $vehicle = $this->builder->createVehicle($this->category, $this->categoryAttrs);

        $vehicle->resolve('file',
            [
                'source' => COMPONENT_CORE_PATH,
                'target' => "return MODX_CORE_PATH . 'components/';"
            ]
        );

        $this->builder->putVehicle($vehicle);

        $this->builder->setPackageAttributes(
            [
                'changelog' => file_get_contents(COMPONENT_DOCS_PATH . 'changelog.txt'),
                'license' => file_get_contents(COMPONENT_DOCS_PATH . 'license.txt'),
                'readme' => file_get_contents(COMPONENT_DOCS_PATH . 'readme.txt'),
            ]
        );

        if ($this->builder->pack()) {
            $this->modx->log(modx::LOG_LEVEL_INFO, 'Пакет готов');
        } else {
            $this->modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке пакета');
        }
    }

    public function __invoke(): void
    {
        $this->initConfig();
        $this->initModx();
        $this->initBuilder();
        $this->initCategory();
        $this->initSnippets();
        $this->initChunks();
        $this->initSystemSettings();
        $this->pack();
    }
}
