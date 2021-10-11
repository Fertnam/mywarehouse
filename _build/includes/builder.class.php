<?php

/**
 * Class Builder
 *
 * @property modx $modx
 * @property modPackageBuilder $builder
 * @property modCategory $category
 * @property array $categoryAttrs
 * @property array $snippets
 * @property array $settings
 * @property array $config
 */
class Builder {
    private modX $modx;
    private modPackageBuilder $builder;
    private modCategory $category;
    private array $categoryAttrs;
    private array $snippets;
    private array $settings;
    private array $config;

    private function __construct() {
        $this->initConfig();
        $this->initModx();
        $this->initBuilder();
        $this->initCategory();
        $this->initSnippets();
        $this->initSystemSettings();
        $this->pack();
    }

    private function initConfig(): void {
        $this->config = require 'builder.config.php';
        $this->config['package']['name_lower'] = strtolower($this->config['package']['name']);

        // Пути
        $root = dirname(dirname(dirname(__FILE__))) . '/';
        $build = $root . '_build/';
        $core = $root . 'core/components/' . $this->config['package']['name_lower'] . '/';

        $this->config['path'] = [
            'root' => $root,
            'build' => $build,
            'data' => $build . 'data/',
            'core' => $core,
            'docs' => $core . 'docs/',
        ];
    }

    private function initModx(): void {
        require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

        $this->modx = new modX();

        $this->modx->initialize('mgr');
        $this->modx->setLogLevel(modx::LOG_LEVEL_INFO);
        $this->modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
    }

    private function initBuilder(): void {
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

    private function initCategory(): void {
        $this->categoryAttrs = [
            xPDOTransport::UNIQUE_KEY => 'category',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
        ];

        $this->category = $this->modx->newObject('modCategory');
        $this->category->set('category', $this->config['package']['name']);

        $this->modx->log(modx::LOG_LEVEL_INFO, 'Создана категория: ' . $this->config['package']['name']);
    }

    private function initSnippets(): void {
        $this->categoryAttrs[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippet'] = [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ];

        $snippets = [];

        /** @noinspection PhpIncludeInspection */
        $tmp = include $this->config['path']['data'] . 'transport.snippets.php';

        foreach ($tmp as $k => $v) {
            /**
             * @var modSnippet $snippet
             * @var modX $modx
             */
            $snippet = $this->modx->newObject('modSnippet');

            //Содержимое сниппета
            $snippetContent = file_get_contents($this->config['path']['core'] . 'elements/snippets/' . $v['file']);

            //Удаление тега php
            preg_match('#\<\?php(.*)#is', $snippetContent, $data);
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

        if (is_array($snippets)) {
            $this->category->addMany($snippets);
            $this->modx->log(modx::LOG_LEVEL_INFO, 'Собрано сниппетов: ' . count($snippets));
        } else {
            $this->modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке сниппетов');
        }
    }

    private function initSystemSettings(): void {
        $attrs = [
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        ];

        $settings = [];

        /** @noinspection PhpIncludeInspection */
        $tmp = include $this->config['path']['data'] . 'transport.settings.php';

        foreach ($tmp as $k => $v) {
            /**
             * @var modSystemSetting $setting
             * @var modX $modx
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

        if (is_array($settings)) {
            foreach ($settings as $setting) {
                $vehicle = $this->builder->createVehicle($setting, $attrs);
                $this->builder->putVehicle($vehicle);
            }

            $this->modx->log(modx::LOG_LEVEL_INFO, 'Собрано системных настроек: ' . count($settings));
        } else {
            $this->modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке системных настроек');
        }
    }

    private function pack(): void {
        $vehicle = $this->builder->createVehicle($this->category, $this->categoryAttrs);

        $vehicle->resolve('file',
            [
                'source' => $this->config['path']['core'],
                'target' => "return MODX_CORE_PATH . 'components/';"
            ]
        );

        $this->builder->putVehicle($vehicle);

        $this->builder->setPackageAttributes(
            [
                'changelog' => file_get_contents($this->config['path']['docs'] . 'changelog.txt'),
                'license' => file_get_contents($this->config['path']['docs'] . 'license.txt'),
                'readme' => file_get_contents($this->config['path']['docs'] . 'readme.txt'),
            ]
        );

        if ($this->builder->pack()) {
            $this->modx->log(modx::LOG_LEVEL_INFO, 'Пакет готов');
        } else {
            $this->modx->log(modx::LOG_LEVEL_ERROR, 'Возникла ошибка при сборке пакета');
        }
    }

    public static function process(): self {
        return new self;
    }
}
