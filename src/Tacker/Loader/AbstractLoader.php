<?php

namespace Tacker\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Tacker\Normalizer;
use Tacker\ResourceCollection;

/**
 * @package Tacker
 */
abstract class AbstractLoader extends FileLoader
{
    protected $normalizer;
    protected $resources;

    public function __construct(
        Normalizer $normalizer,
        FileLocatorInterface $locator,
        ResourceCollection $resources
    ) {
        $this->normalizer = $normalizer;
        $this->locator = $locator;
        $this->resources = $resources;
    }

    public function load($resource, $type = null)
    {
        $resource = $this->locator->locate($resource);

        $this->resources->add(new FileResource($resource));

        return $this->parse($this->read($resource), $resource);
    }

    protected function parse(array $parameters, $resource)
    {
        if (!isset($parameters['@import'])) {
            return $parameters;
        }

        $import = $parameters['@import'];

        unset($parameters['@import']);

        $this->setCurrentDir(dirname($import));

        return array_replace($this->import($import, null, false, $resource), $parameters);
    }

    abstract protected function read($resource);
}
