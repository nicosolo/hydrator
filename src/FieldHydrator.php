<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/29/17
 * Time: 10:04 AM
 */

namespace Clea\Hydrator;

/**
 * Class FieldHydrator
 *
 * this class represent a property and field in an array
 * @package Clea\Hydrator
 */
class FieldHydrator
{

    const TYPE_COLLECTION = 1;
    const TYPE_TRANSFORM = 2;
    const TYPE_HYDRATE = 3;
    const TYPE_SET = 4;
    const TYPE_NOTHING = 5;


    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string|null
     */
    private $collectionType;

    /**
     * @var string|null
     */
    private $objectType;
    /**
     * @var \ReflectionMethod|null
     */
    private $setter;

    /**
     * @var string
     */
    private $className;

    /**
     * FieldHydrator constructor.
     * @param string $className
     * @param string $key
     * @param int $type
     * @param null|\ReflectionMethod $setter
     * @param null|string $objectType
     * @param null|string $collectionType
     */
    public function __construct(string $className,string $key, int $type, ?\ReflectionMethod $setter = null, ?string $objectType = null, ?string $collectionType = null)
    {
        $this->type = $type;
        $this->key = $key;
        $this->objectType = $objectType;
        $this->collectionType = $collectionType;
        $this->setter = $setter;
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }



    /**
     * @return null|\ReflectionMethod
     */
    public function getSetter(): ?\ReflectionMethod
    {
        return $this->setter;
    }

    /**
     * @param null|\ReflectionMethod $setter
     */
    public function setSetter(?\ReflectionMethod $setter)
    {
        $this->setter = $setter;
    }





    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getCollectionType(): ?string
    {
        return $this->collectionType;
    }

    /**
     * @param null|string $collectionType
     */
    public function setCollectionType(?string $collectionType)
    {
        $this->collectionType = $collectionType;
    }

    /**
     * @return null|string
     */
    public function getObjectType(): ?string
    {
        return $this->objectType;
    }

    /**
     * @param null|string $objectType
     */
    public function setObjectType(?string $objectType)
    {
        $this->objectType = $objectType;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className)
    {
        $this->className = $className;
    }



}