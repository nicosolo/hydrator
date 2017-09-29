<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/27/17
 * Time: 7:52 PM
 */

namespace Test\Classes;


class Number
{
    /**
     * @var int
     */
    public $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function increment(){
        $this->number++;
    }

    public function decrement(){
        $this->number--;
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }

}