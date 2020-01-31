<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 28/01/2020
 * Time: 16:09
 */

namespace SaltId\ElasticSearchBundle\Controller;

use Pimcore\Event\DataObjectEvents;
use SaltId\ElasticSearchBundle\EventListener\DataObjectEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dataobjectevent")
 */
class DataObjectEventController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/list", methods={"GET"})
     * @throws \ReflectionException
     */
    public function listAction(Request $request)
    {
        $res = [];
        $reflection = new \ReflectionClass(new DataObjectEvents());

        $dataObjectEventListener = new DataObjectEventListener();

        if (!$reflection) {
            return $this->json($res, 200);
        }

        foreach ($reflection->getConstants() as $key => $constant) {

            $tableFieldPrefix = 'onDataObject';
            $keyAsTableField = strtolower($key);

            $tableField = $tableFieldPrefix .
                str_replace('_', '', ucwords($keyAsTableField, '_'));

            if (!method_exists($dataObjectEventListener, $tableField)) {
                continue;
            }

            $res[] = [
                'method' => $tableField,
                'event' => $constant
            ];
        }

        return $this->json($res, 200);
    }
}