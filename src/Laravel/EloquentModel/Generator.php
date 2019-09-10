<?php

namespace Laradic\Generators\Laravel\EloquentModel;

use Illuminate\Support\Str;
use Laradic\Generators\Core\Elements\ClassElement;
use Laradic\Generators\Laravel\EloquentModel\Exception\GeneratorException;

/**
 * Class Generator
 *
 * @package Krlove\Generator
 */
class Generator
{
    /**
     * @var EloquentElementBuilder
     */
    protected $builder;

    /**
     * @var TypeRegistry
     */
    protected $typeRegistry;

    /**
     * Generator constructor.
     *
     * @param EloquentElementBuilder $builder
     * @param TypeRegistry           $typeRegistry
     */
    public function __construct(EloquentElementBuilder $builder, TypeRegistry $typeRegistry)
    {
        $this->builder      = $builder;
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @param Config $config
     * @return ClassElement
     * @throws GeneratorException
     */
    public function generateElement(Config $config)
    {
        $this->registerUserTypes($config);

        $element   = $this->builder->createElement($config);
        $content = $element->render();

        $outputPath = $this->resolveOutputPath($config);
        if ($config->get('backup') && file_exists($outputPath)) {
            rename($outputPath, $outputPath . '~');
        }
        file_put_contents($outputPath, $content);

        return $element;
    }

    /**
     * @param Config $config
     * @return string
     * @throws GeneratorException
     */
    protected function resolveOutputPath(Config $config)
    {
        $path = $config->get('output_path');
        if ($path === null || Str::startsWith($path, '/')) {
            if (function_exists('app_path')) {
                $path = app_path($path);
            } else {
                $path = app('path') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
            }
        }

        if ( ! mkdir($path, 0777, true) && ! is_dir($path)) {
            throw new GeneratorException(sprintf('Could not create directory %s', $path));
        }

        if ( ! is_writable($path)) {
            throw new GeneratorException(sprintf('%s is not writeable', $path));
        }

        return $path . '/' . $config->get('class_name') . '.php';
    }

    /**
     * @param Config $config
     */
    protected function registerUserTypes(Config $config)
    {
        $userTypes = $config->get('db_types');
        if ($userTypes && is_array($userTypes)) {
            $connection = $config->get('connection');

            foreach ($userTypes as $type => $value) {
                $this->typeRegistry->registerType($type, $value, $connection);
            }
        }
    }
}
