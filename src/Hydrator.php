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

    private $collectionName = "array";


    public function __construct(?string $collectionName = "array", ?TransformerInterface $transformer = null)
    {

        $this->collectionName = $collectionName;
        if ($transformer == null) {
            $this->transformer = new Transformer();
        } else {
            $this->transformer = $transformer;
        }

    }

    /**
     * @param string $target
     * @param array $data
     * @return object|string
     */
    public function hydrate(string $target, array $data)
    {
        $reflection = $this->getReflectionClass($target);
        $object = is_object($target) ? $target : $reflection->newInstanceWithoutConstructor();
        foreach ($data as $name => $value) {
            $this->hydrateField($name, $reflection, $value, $object);
        }
        return $object;
    }

    /**
     * @param string $fieldName
     * @param \ReflectionClass $reflection
     * @param $value
     * @param $object
     * @param null $parent
     */
    private function hydrateField(string $fieldName, \ReflectionClass $reflection, $value, $object, $parent = null): void
    {
        $propertyComment = null;
        if ($reflection->hasProperty($fieldName)) {
            $propertyComment = $reflection->getProperty($fieldName)->getDocComment();
            if (strpos($propertyComment, "@noHydrate")) {
                return;
            }
        } else {
            return;
        }

        $setter = $this->getSetter($fieldName);
        $getter = $this->getGetter($fieldName);


        if ($reflection->hasMethod($setter)) {

            $methodSetter = $reflection->getMethod($setter);

            if ($reflection->hasMethod($getter)) {

                $methodGetter = $reflection->getMethod($getter);

                if ($type = $methodGetter->getReturnType()) {
                    if ($this->transformer->hasType($type->getName())) {
                        $methodSetter->invoke($object, $this->transformer->transform($type->getName(), $value));
                        return;
                    }

                    if ($type->getName() == $this->collectionName) {

                        $type = $this->getTypeOfCollection($propertyComment);
                        if ($type) {
                            $self = $this;
                            $data = array_map(function ($item) use ($self, $type) {
                                return $self->hydrate($type, $item);
                            }, $value);
                            $methodSetter->invoke($object, $data);
                            return;
                        }
                    } elseif ($type->getName()) {
                        $methodSetter->invoke($object, $this->hydrate($type->getName(), $value));
                        return;
                    }
                }

            }

            $methodSetter->invoke($object, $value);
            return;
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

    public function getTypeOfCollection(string $comment): ?string
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
        return "set" . $this->getProperty($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function getGetter(string $fieldName): string
    {
        return "get" . $this->getProperty($fieldName);
    }

    /**
     * @param string $fieldName
     * @return string
     */
    private function getProperty(string $fieldName): string
    {
        return join("", array_map("ucfirst", explode("_", $fieldName)));
    }
}