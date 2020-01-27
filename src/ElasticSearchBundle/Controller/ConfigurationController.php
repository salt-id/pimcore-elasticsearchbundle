<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 14/01/2020
 * Time: 14:31
 */

namespace SaltId\ElasticSearchBundle\Controller;

use Pimcore\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @Route("/configuration")
 */
class ConfigurationController extends AbstractController
{
    private $configFile = self::SYNONYM_PATH . '/config.yml';


    private $configs = [
        'general' => [
            'hostorip' => '127.0.0.1',
            'port' => '9200',
            'httpBasicAuthUser' => null,
            'httpBasicAuthPassword' => null
        ]
    ];

    /**
     * @param Request $request
     *
     * @Route("/")
     */
    public function getConfiguration(Request $request)
    {
        try {
            $config = Yaml::parseFile($this->configFile) ?: $this->configs;
            File::put($this->configFile, Yaml::dump($config, 5));

        } catch (ParseException $parseException) {
            if (!$fileExist = file_exists($this->configFile)) {
                File::put($this->configFile, Yaml::dump($this->configs, 5));
            }

            $config = $this->configs;
        }

        return $this->json(['values' => $config], 200);
    }

    /**
     * @param Request $request
     *
     * @Route("/save")
     */
    public function putConfiguration(Request $request)
    {
        $existing = Yaml::parseFile($this->configFile) ? Yaml::parseFile($this->configFile) : $this->configs;

        $decode = json_decode($request->get('data'), true);
        $data = $this->exploder($decode);

        $settings = array_replace_recursive($existing, $data);
        $save = File::put($this->configFile, Yaml::dump($settings, 5));

        return $this->json([
            'success' => $save
        ], 200);
    }

    public function exploder(array $data)
    {
        $tmp = [];
        if (!$data) {
            return $tmp;
        }

        foreach ($data as $key => $datum) {
            $explodeKey = explode('.', $key);
            $tmp[$explodeKey[0]][$explodeKey[1]] = $datum;
        }

        return $tmp;
    }
}