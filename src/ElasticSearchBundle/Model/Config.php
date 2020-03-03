<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 28/02/2020
 * Time: 16:01
 */

namespace SaltId\ElasticSearchBundle\Model;

use Pimcore\Model\AbstractModel;

class Config extends AbstractModel
{
    public $id;

    public $name;

    public $hostorip;

    public $port;

    public $httpBasicAuthUser;

    public $httpBasicAuthPassword;

    public $index;

    public static function getById($id)
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        } catch (\Exception $e) {

        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getHostorip()
    {
        return $this->hostorip;
    }

    /**
     * @param mixed $hostorip
     */
    public function setHostorip($hostorip): void
    {
        $this->hostorip = $hostorip;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getHttpBasicAuthUser()
    {
        return $this->httpBasicAuthUser;
    }

    /**
     * @param mixed $httpBasicAuthUser
     */
    public function setHttpBasicAuthUser($httpBasicAuthUser): void
    {
        $this->httpBasicAuthUser = $httpBasicAuthUser;
    }

    /**
     * @return mixed
     */
    public function getHttpBasicAuthPassword()
    {
        return $this->httpBasicAuthPassword;
    }

    /**
     * @param mixed $httpBasicAuthPassword
     */
    public function setHttpBasicAuthPassword($httpBasicAuthPassword): void
    {
        $this->httpBasicAuthPassword = $httpBasicAuthPassword;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }
}