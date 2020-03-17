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

class StopwordsController extends AbstractController
{
    const STOPWORDS_FILE = 'stopwords.txt';

    /**
     * @Route("/stopwords", methods={"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function stopwordsGetAction(Request $request)
    {
        $data['default'] = '';

        try {
            $onFileSystem = file_exists(self::SYNONYM_PATH . '/' . self::STOPWORDS_FILE);

            if ($onFileSystem) {
                $data['default'] = file_get_contents(self::SYNONYM_PATH . '/' . self::STOPWORDS_FILE);
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
     * @Route("/stopwords", methods={"PUT"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function stopwordsPutAction(Request $request)
    {
        $values = $request->get('data');
        if (!is_array($values)) {
            $values = [];
        }

        File::put(self::SYNONYM_PATH . '/' . self::STOPWORDS_FILE, $values['default']);

        return $this->json([
            'success' => true
        ], 200);
    }
}