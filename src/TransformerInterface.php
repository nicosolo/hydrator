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
    public function transform(string $type, $value);

    public function hasType(string $type): bool;
}