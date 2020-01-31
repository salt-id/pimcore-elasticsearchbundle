<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 28/01/2020
 * Time: 15:55
 */

namespace SaltId\ElasticSearchBundle\Model\IndexRule;

use Pimcore\Model\Listing\AbstractListing;

class Listing extends AbstractListing
{
    /**
     * List of indexRule.
     *
     * @var array
     */
    protected $indexRules = null;

    public function setIndexRules(array $indexRules)
    {
        $this->indexRules = $indexRules;

        return $this;
    }

    public function getIndexRules()
    {
        if ($this->indexRules === null) {
            $this->getDao()->load();
        }

        return $this->indexRules;
    }
}