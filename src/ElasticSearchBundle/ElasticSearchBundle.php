<?php

namespace SaltId\ElasticSearchBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class ElasticSearchBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getNiceName()
    {
        return 'Elasticsearch';
    }

    public function getDescription()
    {
        return 'Manage elasticsearch in Pimcore';
    }

    public function getJsPaths()
    {
        return [
            '/bundles/elasticsearch/js/pimcore/elasticsearch/indexConfigDialog.js',
            '/bundles/elasticsearch/js/pimcore/elasticsearch/index.js',
            '/bundles/elasticsearch/js/pimcore/elasticsearch/indexItem.js',
            '/bundles/elasticsearch/js/pimcore/elasticsearch/configuration.js',
            '/bundles/elasticsearch/js/pimcore/elasticsearch/synonym.js',
            '/bundles/elasticsearch/js/pimcore/startup.js'
        ];
    }

    public function getCssPaths()
    {
        return [
            '/bundles/elasticsearch/css/icon.css',
        ];
    }

    /**
     * Returns the composer package name used to resolve the version
     *
     * @return string
     */
    protected function getComposerPackageName(): string
    {
        return 'saltid/pimcore-elasticsearchbundle';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}
