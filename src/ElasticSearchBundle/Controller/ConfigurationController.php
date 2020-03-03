<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 14/01/2020
 * Time: 14:31
 */

namespace SaltId\ElasticSearchBundle\Controller;

use Pimcore\File;
use SaltId\ElasticSearchBundle\Model\Config;
use SaltId\ElasticSearchBundle\Resolver\ElasticSearchConfigurationResolver;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @Route("/configuration")
 */
class ConfigurationController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @Route("/")
     * @return JsonResponse
     */
    public function getConfiguration(Request $request)
    {
        $getConfig = Config::getById(1);

        if (!$getConfig) {
            $getConfig = $this->getDefaultConfiguration();
        }

        return $this->json(['values' => $getConfig ? ['general' => $getConfig->getObjectVars()] : null], 200);
    }

    /**
     * @param Request $request
     *
     * @Route("/save")
     * @return JsonResponse
     */
    public function saveConfiguration(Request $request)
    {
        $decode = json_decode($request->get('data'), true);
        $data = $this->exploder($decode);

        $config = Config::getById(1);
        $success = true;
        $message = null;

        try {
            $setConfig = $config ? $config->setValues($data['general']) : false;

            if ($setConfig) {
                $message = 'Saved successfully';
                $config->save();
            }
        } catch (\Exception $exception) {
            $success = false;
            $message = $exception->getMessage();
        }
        return $this->json(['success' => $success, 'message' => $message], 200);
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

    private function getDefaultConfiguration()
    {
        $config = new Config();
        $config->setName('general');
        $config->setHostorip('127.0.0.1');
        $config->setPort('9200');
        $config->setHttpBasicAuthUser('admin');
        $config->setHttpBasicAuthPassword('admin');
        $config->setIndex('elastic');
        try {
            $config->save();
            return $config;
        } catch (\Exception $exception) {
            return null;
        }
    }
}