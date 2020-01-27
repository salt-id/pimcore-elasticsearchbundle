<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 14/01/2020
 * Time: 16:09
 */

namespace SaltId\ElasticSearchBundle\Model;

use Symfony\Component\Yaml\Yaml;

class AbstractModel
{
    public $parent;

    public $data;

    public function __construct()
    {

    }
}