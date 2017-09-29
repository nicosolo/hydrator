<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/25/17
 * Time: 10:55 PM
 */

namespace Clea\Hydrator;


use DI\Annotation\Inject;

class Hydrator
{
    /**
     * @var array
     */
    private $reflectionClassMap;

    /**
     * @var Transformer
     */
    private $transformer;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var array
     */
    private $hydratedProperties;

    /**
     * @var bool
     */
    private $cache;


    /**
     * Hydrator constructor.
     * @param array|null $settings
     *
     * @param TransformerInterface|null $transformer
     */
    public function __construct(array $settings = [], ?TransformerInterface $transformer = null)
    {

        $defaultSettings = [
            "additional_type" => [],
            "cache" => true
        ];


        $this->settings = array_merge($defaultSettings, $settings);

        $this->setCache((bool)$this->settings["cache"]);

        if ($transformer == null) {
            $this->transformer = new Transformer($this->settings["additional_type"]);
        } else {
            $this->transformer = $transformer;
        }

    }

    /**
     * Returns an entry of the container by its name.
     *
     * @param mixed $target Entry name or a class name.
     * @param array $data
     * @return mixed
     */
    public function hydrate($target, array $data)
    {

        $reflection = $this->getReflectionClass($target);
        $object = is_object($target) ? $target : $reflection->newInstanceWithoutConstructor();

        if ($this->hasCache()) {

            foreach ($data as $name => $value) {
                if (!$property = $this->getHydratedProperty($reflection->getName(), $name)) {

                    $property = $this->getPropertyHydrator($name, $reflection);
                    $this->addHydratedProperty($property);

                }

                $this->hydrateProperty($property, $value, $object);
            }

        } else {
            foreach ($data as $name => $value) {
                $this->hydrateProperty($this->getPropertyHydrator($name, $reflection), $value, $object);
            }
        }

        return $object;
    }

    /**
     * @param string $fieldName
     * @param \ReflectionClass $reflection
     * @return FieldHydrator
     */
    private function getPropertyHydrator(string $fieldName, \ReflectionClass $reflection): FieldHydrator
    {


        $className = $reflection->getName();
        $propertyComment = null;
        $propertyName = $this->getPropertyName($fieldName);

        if ($reflection->hasProperty($propertyName)) {
            $propertyComment = $reflection->getProperty($propertyName)->getDocComment();
            if (strpos($propertyComment, "@noHydrated")) {
                return new FieldHydrator($className, $fieldName, FieldHydrator::TYPE_NOTHING);
            }
        } else {
            return new FieldHydrator($className, $fieldName, FieldHydrator::TYPE_NOTHING);
        }


        $setter = $this->getSetter($fieldName);
        $getter = $this->getGetter($fieldName);


        if ($reflection->hasMethod($setter)) {

            $methodSetter = $reflection->getMethod($setter);

            if ($reflection->hasMethod($getter)) {

                $methodGetter = $reflection->getMethod($getter);

                if ($type = $methodGetter->getReturnType()) {
                    if ($this->transformer->hasType($type->getName())) {
                        return new FieldHydrator(
                            $className,
                            $fieldName,
                            FieldHydrator::TYPE_TRANSFORM,
                            $methodSetter,
                            $type->getName()
                        );
                    }

                    if (
                        $type->getName() == "array" or
                        $this->getReflectionClass($type->getName())->implementsInterface(\ArrayAccess::class)
                    ) {

                        $objectType = $this->getTypeOfCollectionObject($propertyComment);
                        if ($objectType) {
                            return new FieldHydrator(
                                $className,
                                $fieldName,
                                FieldHydrator::TYPE_COLLECTION,
                                $methodSetter,
                                $objectType,
                                $type->getName()
                            );
                        } else {
                            return new FieldHydrator(
                                $className,
                                $fieldName,
                                FieldHydrator::TYPE_SET,
                                $methodSetter
                            );
                        }
                    } elseif ($type->getName()) {
                        return new FieldHydrator(
                            $className,
                            $fieldName,
                            FieldHydrator::TYPE_HYDRATE,
                            $methodSetter,
                            $type->getName()
                        );

                    }
                }

            }

            return new FieldHydrator($className, $fieldName, FieldHydrator::TYPE_SET, $methodSetter);
        }

        return new FieldHydrator($className, $fieldName, FieldHydrator::TYPE_NOTHING);

    }

    /**
     * @param FieldHydrator $propertyHydrator
     * @param $value
     * @param $object
     * @return mixed
     */
    private function hydrateProperty(FieldHydrator $propertyHydrator, $value, $object)
    {

        if ($propertyHydrator->getType() == FieldHydrator::TYPE_NOTHING) {
            return;
        }

        if ($propertyHydrator->getType() == FieldHydrator::TYPE_TRANSFORM) {
            return $propertyHydrator->getSetter()->invoke($object, $this->transformer->transform($propertyHydrator->getObjectType(), $value));
        }

        if ($propertyHydrator->getType() == FieldHydrator::TYPE_HYDRATE) {
            if (is_iterable($value)) {
                return $propertyHydrator->getSetter()->invoke($object, $this->hydrate($propertyHydrator->getObjectType(), $value));
            }

        }

        if ($propertyHydrator->getType() == FieldHydrator::TYPE_COLLECTION) {

            if (is_iterable($value)) {
                $collectionType = $propertyHydrator->getCollectionType();
                if ($collectionType == "array") {
                    $values = [];
                } else {
                    $values = new $collectionType();
                }

                foreach ($value as $key => $item) {
                    $values[$key] = $this->hydrate($propertyHydrator->getObjectType(), $item);
                }
            }

            return $propertyHydrator->getSetter()->invoke($object, $values);

        }

        if ($propertyHydrator->getType() == FieldHydrator::TYPE_SET) {
            return $propertyHydrator->getSetter()->invoke($object, $value);
        }

    }


    /**
     * @param string|object $target
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    private function getReflectionClass($target): \ReflectionClass
    {
        $className = is_object($target) ? get_class($target) : $target;
        if (!isset($this->reflectionClassMap[$className])) {

            $this->reflectionClassMap[$className] = new \ReflectionClass($className);

        }
        return $this->reflectionClassMap[$className];
    }

    private function getTypeOfCollectionObject(string $comment): ?string
    {
        if (preg_match("#@var (.*)\[\]#", $comment, $matches)) {
            return $matches[1] ?? null;
        }
        return null;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function getSetter(string $fieldName): string
    {
        return "set" . $this->toCamelCase($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function getGetter(string $fieldName): string
    {
        return "get" . $this->toCamelCase($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function toCamelCase(string $fieldName): string
    {
        return join("", array_map("ucfirst", explode("_", $fieldName)));
    }

    /**
     * @param string $property
     * @return string
     */
    public function getPropertyName(string $property)
    {
        return lcfirst($this->toCamelCase($property));
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return bool
     */
    public function hasCache(): bool
    {
        return $this->cache;
    }

    /**
     * @param bool $cache
     */
    public function setCache(bool $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param FieldHydrator|null $propertyHydrator
     */
    private function addHydratedProperty(?FieldHydrator $propertyHydrator = null)
    {

        $this->hydratedProperties[$propertyHydrator->getClassName()][$propertyHydrator->getKey()] = $propertyHydrator;


    }

    /**
     * @param string $className
     * @param string $fieldName
     * @return FieldHydrator|null
     */
    private function getHydratedProperty(string $className, string $fieldName): ?FieldHydrator
    {
        return $this->hydratedProperties[$className][$fieldName] ?? null;
    }


}