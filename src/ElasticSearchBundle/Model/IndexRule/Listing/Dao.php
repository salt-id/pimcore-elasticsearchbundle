<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 28/01/2020
 * Time: 15:55
 */

namespace SaltId\ElasticSearchBundle\Model\IndexRule\Listing;

use Pimcore\Model\Listing\Dao\AbstractDao;
use SaltId\ElasticSearchBundle\Model\IndexRule;

class Dao extends AbstractDao
{
    public function load()
    {
        $ids = $this->db->fetchCol('SELECT id FROM bundle_elasticsearch_index_rule' . $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(), $this->model->getConditionVariables());

        $indexRules = [];
        foreach ($ids as $id) {
            $indexRules[] = IndexRule::getById($id);
        }

        $this->model->setIndexRules($indexRules);

        return $indexRules;
    }
}