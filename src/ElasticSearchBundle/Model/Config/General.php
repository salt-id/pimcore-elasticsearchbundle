<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 14/01/2020
 * Time: 16:03
 */

namespace SaltId\ElasticSearchBundle\Model\Config;

use SaltId\ElasticSearchBundle\Model\AbstractModel;

class General extends AbstractModel
{
    public function getHostOrIp()
    {
        $this->data;
    }

    public function getPort()
    {
        return '9200';
    }

    public function getHttpBasicAuthUser()
    {
        return 'admin';
    }

    public function getHttpBasicAuthPassword()
    {
        return 'admin';
    }
}