<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 27/01/2020
 * Time: 14:35
 */

namespace SaltId\ElasticSearchBundle\EventListener;

use Carbon\Carbon;
use Pimcore\Event\Model\{ElementEventInterface, DataObjectEvent};
use Pimcore\Log\Simple;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use SaltId\ElasticSearchBundle\Services\ElasticSearch;
use SaltId\ElasticSearchBundle\Model\IndexRule;
use SaltId\ElasticSearchBundle\Tool\Config;

class DataObjectEventListener
{
    /** @var ElasticSearch $elasticSearch */
    private $elasticSearch;

    public function __construct(ElasticSearch $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }


    public function onDataObjectPreAdd(ElementEventInterface $elementEvent)
    {
        if ($elementEvent instanceof DataObjectEvent) {
            $object = $elementEvent->getObject();
            // @todo do magic here.
        }
    }

    public function onDataObjectPostAdd(ElementEventInterface $elementEvent)
    {
        if ($elementEvent instanceof DataObjectEvent) {
            $object = $elementEvent->getObject();

            $indexRuleListing = new IndexRule\Listing();
            $indexRuleListing->setCondition('onDataObjectPostAdd = ? AND active = ?', [1, 1]);

            if (!$indexRuleListing->load()) {
                return;
            }

            /** @var IndexRule $indexRule */
            foreach ($indexRuleListing->load() as $indexRule) {
                $className = $indexRule->getClassName();
                $classNameSpace = '\\Pimcore\\Model\\DataObject\\' . $className;

                $newClass = new $classNameSpace();

                if ($object instanceof $newClass) {
                    $this->doIndexToElasticSearch($className, $object, $indexRule);
                }
            }
        }
    }

    public function onDataObjectPreUpdate(ElementEventInterface $elementEvent)
    {
        if ($elementEvent instanceof DataObjectEvent) {
            $object = $elementEvent->getObject();
            // @todo do magic here.
        }
    }

    public function onDataObjectPostUpdate(ElementEventInterface $elementEvent)
    {
        if ($elementEvent instanceof DataObjectEvent) {
            $object = $elementEvent->getObject();

            $indexRuleListing = new IndexRule\Listing();
            $indexRuleListing->setCondition('onDataObjectPostUpdate = ? AND active = ?', [1, 1]);

            if (!$indexRuleListing->load()) {
                return;
            }

            /** @var IndexRule $indexRule */
            foreach ($indexRuleListing->load() as $indexRule) {
                $className = $indexRule->getClassName();
                $classNameSpace = '\\Pimcore\\Model\\DataObject\\' . $className;

                $newClass = new $classNameSpace();

                if ($object instanceof $newClass) {
                    $this->doIndexToElasticSearch($className, $object, $indexRule);
                }
            }
        }
    }

    private function doIndexToElasticSearch(string $className, AbstractObject $object, IndexRule $indexRule)
    {

        /** @var ClassDefinition $classDefinition */
        $classDefinition = ClassDefinition::getByName($className);
        if (!$classDefinition) {
            return;
        }

        $fieldConfig = $indexRule->getClassFieldConfig() ?? null;
        $decodeFieldConfig = json_decode($fieldConfig, true);
        $fields = [];
        foreach ($decodeFieldConfig as $fieldConfigx) {
            $fields[] = $fieldConfigx['key'];
        }

        // @todo this block code is useless because getClassFieldConfig() already provide data that we need
        // @todo or it can be useful for checking valid field ?
        $existingFields = [];
        foreach ($classDefinition->getFieldDefinitions() as $fieldDefinition) {
            if (!in_array($fieldDefinition->getName(), $fields, false)) {
                continue;
            }
            $fieldName = $fieldDefinition->getName();
            $getter = 'get' . ucfirst($fieldName);
            if (!method_exists($object, $getter)) {
                continue;
            }
            $existingFields[$fieldName] = $object->$getter();
        }
        if (method_exists($object, 'getRouter')) {
            $existingFields['url'] = $object->getRouter();
        }
        // @todo think about it.

        // @todo send to elasticsearch.
        $putIndex = $this->elasticSearch
            ->createDocument(Config::getConfigIndex(), strtolower($className), $object->getId(), $existingFields);

        $fileLogName = 'BUNDLE_ELASTICSEARCH_';
        $fileLogName .= strtoupper($className) . '_';
        $fileLogName .= strtoupper($putIndex['result'] ?? '*') . '_';
        $fileLogName .= Carbon::now()->format('dmY');

        Simple::log($fileLogName, json_encode($putIndex));
    }
}