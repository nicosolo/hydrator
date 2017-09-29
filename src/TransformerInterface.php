<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/26/17
 * Time: 12:13 AM
 */

namespace Clea\Hydrator;


interface TransformerInterface
{

    /**
     * TransformerInterface constructor.
     * @param array $additionalType
     */
    public function __construct(array $additionalType = []);
    /**
     * @param callable|string $type
     * @param $value
     * @return mixed
     */
    public function transform($type, $value);

    /**
     * @param string $type
     * @return bool
     */
    public function hasType(string $type): bool;
}