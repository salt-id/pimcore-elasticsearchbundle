<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 28/01/2020
 * Time: 15:04
 */

namespace SaltId\ElasticSearchBundle\Model;

use Pimcore\Model\AbstractModel;

class IndexRule extends AbstractModel
{
    /** @var integer $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var boolean $onDataObjectPreAdd */
    public $onDataObjectPreAdd;

    /** @var boolean $onDataObjectPostAdd */
    public $onDataObjectPostAdd;

    /** @var boolean $onDataObjectPreUpdate */
    public $onDataObjectPreUpdate;

    /** @var boolean $onDataObjectPostUpdate */
    public $onDataObjectPostUpdate;

    /** @var string $className */
    public $className;

    /** @var string $classFieldConfig */
    public $classFieldConfig;

    /** @var boolean $active */
    public $active;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function getOnDataObjectPreAdd()
    {
        return $this->onDataObjectPreAdd;
    }

    /**
     * @param bool $onDataObjectPreAdd
     */
    public function setOnDataObjectPreAdd($onDataObjectPreAdd): void
    {
        $this->onDataObjectPreAdd = $onDataObjectPreAdd;
    }

    /**
     * @return bool
     */
    public function getOnDataObjectPostAdd()
    {
        return $this->onDataObjectPostAdd;
    }

    /**
     * @param bool $onDataObjectPostAdd
     */
    public function setOnDataObjectPostAdd($onDataObjectPostAdd): void
    {
        $this->onDataObjectPostAdd = $onDataObjectPostAdd;
    }

    /**
     * @return bool
     */
    public function getOnDataObjectPreUpdate()
    {
        return $this->onDataObjectPreUpdate;
    }

    /**
     * @param bool $onDataObjectPreUpdate
     */
    public function setOnDataObjectPreUpdate($onDataObjectPreUpdate): void
    {
        $this->onDataObjectPreUpdate = $onDataObjectPreUpdate;
    }

    /**
     * @return bool
     */
    public function getOnDataObjectPostUpdate()
    {
        return $this->onDataObjectPostUpdate;
    }

    /**
     * @param bool $onDataObjectPostUpdate
     */
    public function setOnDataObjectPostUpdate($onDataObjectPostUpdate): void
    {
        $this->onDataObjectPostUpdate = $onDataObjectPostUpdate;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName($className): void
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassFieldConfig()
    {
        return $this->classFieldConfig;
    }

    /**
     * @param string $classFieldConfig
     */
    public function setClassFieldConfig($classFieldConfig): void
    {
        $this->classFieldConfig = $classFieldConfig;
    }


    /**
     * @return bool
     */
    public function getActive()
    {
        return (bool) $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }
}