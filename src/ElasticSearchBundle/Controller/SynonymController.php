<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 07/01/2020
 * Time: 17:59
 */

namespace SaltId\ElasticSearchBundle\Controller;

use Pimcore\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SynonymController extends AbstractController
{
    const SYNONYM_FILE = 'synonym.txt';

    /**
     * @Route("/synonym", methods={"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function synonymGetAction(Request $request)
    {
        $data['default'] = '';

        try {
            $onFileSystem = file_exists(self::SYNONYM_PATH . '/' . self::SYNONYM_FILE);

            if ($onFileSystem) {
                $data['default'] = file_get_contents(self::SYNONYM_PATH . '/' . self::SYNONYM_FILE);
            }
        } catch (\Exception $exception) {

        }

        return $this->json([
            'success' => true,
            'data' => $data,
            'onFileSystem' => false
        ], 200);
    }

    /**
     * @Route("/synonym", methods={"PUT"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function synonymPutAction(Request $request)
    {
        $values = $request->get('data');
        if (!is_array($values)) {
            $values = [];
        }

        File::put(self::SYNONYM_PATH . '/' . self::SYNONYM_FILE, $values['default']);

        return $this->json([
            'success' => true
        ], 200);
    }
}