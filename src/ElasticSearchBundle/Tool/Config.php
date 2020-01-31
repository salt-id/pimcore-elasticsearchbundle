<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 31/01/2020
 * Time: 15:15
 */

namespace SaltId\ElasticSearchBundle\Tool;

use Pimcore\File;
use SaltId\ElasticSearchBundle\Resolver\ElasticSearchConfigurationResolver;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Config
{
    const ELASTICSEARCH_BUNDLE_VAR_PATH = PIMCORE_PRIVATE_VAR . '/bundles/pimcore-elasticsearchbundle';

    const CONFIG_FILE = self::ELASTICSEARCH_BUNDLE_VAR_PATH  . '/config.yml';

    public const DEFAULT_CONFIG = [
        'general' => [
            'hostorip' => '127.0.0.1',
            'port' => '9200',
            'httpBasicAuthUser' => null,
            'httpBasicAuthPassword' => null,
            'index' => null
        ]
    ];

    public static function getConfig()
    {
        try {
            $config = Yaml::parseFile(self::CONFIG_FILE) ?: self::DEFAULT_CONFIG;
            File::put(self::CONFIG_FILE, Yaml::dump($config, 5));

        } catch (ParseException $parseException) {
            if (!$fileExist = file_exists(self::CONFIG_FILE)) {
                File::put(self::CONFIG_FILE, Yaml::dump(self::DEFAULT_CONFIG, 5));
            }

            $config = self::DEFAULT_CONFIG;
        }

        try {
            $resolver = new ElasticSearchConfigurationResolver($config);
        } catch (\Exception $exception) {

        }

        return $config;
    }

    public static function setConfig(array $config)
    {
        $existing = self::getConfig();

        $settings = array_replace_recursive($existing, $config);
        $success = false;
        $message = null;
        try {
            $resolver = new ElasticSearchConfigurationResolver($settings);
            $success = true;
            $message = 'Saved Successfully';
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }
        $save = File::put(self::CONFIG_FILE, Yaml::dump($settings, 5));

        return ['success' => $success, 'message' => $message];
    }

    public static function getConfigHostOrIp()
    {
        return self::getConfig()['general']['hostorip'] ?: null;
    }

    public static function getConfigPort()
    {
        return self::getConfig()['general']['port'] ?: null;
    }

    public static function getConfigHttpBasicAuthUser()
    {
        return self::getConfig()['general']['httpBasicAuthUser'] ?: null;
    }

    public static function getConfigHttpBasicAuthPassword()
    {
        return self::getConfig()['general']['httpBasicAuthPassword'] ?: null;
    }

    public static function getConfigIndex()
    {
        return self::getConfig()['general']['index'] ?: null;
    }
}