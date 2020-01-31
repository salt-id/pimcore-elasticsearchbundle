<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 14/01/2020
 * Time: 14:31
 */

namespace SaltId\ElasticSearchBundle\Controller;

use Pimcore\File;
use SaltId\ElasticSearchBundle\Resolver\ElasticSearchConfigurationResolver;
use SaltId\ElasticSearchBundle\Tool\Config;
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
        $getConfig = Config::getConfig();
        return $this->json(['values' => $getConfig], 200);
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

        $setConfig = Config::setConfig($data);
        return $this->json($setConfig, 200);
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