<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/25/17
 * Time: 11:36 PM
 */

namespace Clea\Hydrator;


class Transformer implements TransformerInterface
{
    public $types = [
        \string::class => "toString",
        \int::class => "toInt",
        \float::class => "toInt",
        \DateTime::class => "toDateTime"
    ];


    /**
     * @param $value
     * @return string
     */
    public function toString($value): string
    {
        return (string)$value;
    }

    /**
     * @param $value
     * @return int
     */
    public function toInt($value): int
    {
        return (int)$value;
    }

    /**
     * @param $value
     * @return \DateTime
     */
    public function toDateTime($value): \DateTime
    {
        return new \DateTime($value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasType(string $name): bool
    {
        return isset($this->types[$name]);
    }

    /**
     * @param string $type
     * @param $value
     * @return mixed
     */
    public function transform(string $type, $value)
    {
        return $this->{$this->types[$type]}($value);
    }
}