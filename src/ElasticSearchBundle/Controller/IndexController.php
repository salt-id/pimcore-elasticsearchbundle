<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 27/01/2020
 * Time: 14:28
 */

namespace SaltId\ElasticSearchBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use SaltId\ElasticSearchBundle\Model\IndexRule;

/**
 * @Route("/indexrule")
 */
class IndexController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @Route("/list", methods={"GET"})
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        //return indexRule dao listing
        $indexRules = [];

        /** @var IndexRule\Listing $list */
        $list = new IndexRule\Listing();

        /** @var IndexRule $indexRule */
        foreach ($list->load() as $indexRule) {
            $fields = get_object_vars($indexRule);
            $text = null;
            foreach ($fields as $key => $field) {
                $getter = 'get' . ucfirst($key);
                if (!method_exists($indexRule, $getter)) {
                    continue;
                }

                $text = $indexRule->getName() ?? '';

                $tmp[$key] = $indexRule->$getter();
            }
            $tmp['text'] = $text;
            $indexRules[] = $tmp;
        }

        return $this->json($indexRules, 200);
    }

    /**
     * @Route("/get", methods={"GET"})
     */
    public function detailAction(Request $request)
    {
        $indexRule = IndexRule::getById($request->get('id'));

        $res = [];

        if ($indexRule) {
            $objvars = get_object_vars($indexRule);

            foreach ($objvars as $k => $objvar) {
                $getter = 'get' . ucfirst($k);

                $res[$k] = $indexRule->$getter();
            }
        }

        return $this->json($res, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route("/save", methods={"PUT"})
     */
    public function saveAction(Request $request)
    {
        $data = json_decode($request->get('data'), true);

        $indexRule = IndexRule::getById($request->get('id'));
        if (!$indexRule) {
            return $this->json(['success' => false, 'message' => 'Index Rule Not Found'], 200);
        }
        $indexRule->setValues($data['settings']);

        $success = false;
        $message = null;
        try {
            $success = true;
            $message = 'Saved successfully.';

            $indexRule->save();
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $message = 'Duplicate data ! Failed to save.';
        }

        return $this->json(['success' => $success, 'message' => $message], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $success = false;

        $indexRule = IndexRule::getById($request->get('id'));
        if ($indexRule) {
            $indexRule->delete();
            $success = true;
        }
        return $this->json(['success' => $success], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/add", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        $indexRule = new IndexRule();
        $indexRule->setName($request->get('name'));

        $success = false;
        $message = null;
        try {
            $indexRule->save();
            $success = true;
            $message = 'Saved successfully';
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        return $this->json(['success' => $success, 'id' => $indexRule->getId(), 'message' => $message]);
    }

    /**
     * @param Request $request
     * @Route("/classfieldsetup/{indexRuleId}", methods={"PUT"})
     * @return JsonResponse
     */
    public function classFieldSetupAction($indexRuleId, Request $request)
    {
        $indexRuleById = IndexRule::getById($indexRuleId);
        if (!$indexRuleId) {
            return $this->json(['success' => false, 'message' => 'Index Rule Not Found'], 200);
        }

        $success = false;
        $message = null;
        try {
            $indexRuleById->setClassFieldConfig($request->get('data'));
            $indexRuleById->save();
            $success = true;
            $message = 'Saved successfully';
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        return $this->json(['success' => $success, 'message' => $message], 200);
    }
}