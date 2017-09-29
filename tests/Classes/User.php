<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/25/17
 * Time: 11:03 PM
 */

namespace Test\Classes;


class User
{
    /**

     * @var string
     */
    private $name;

    /**
     * @noHydrated
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \Test\Classes\Comment[]
     */
    private $comments;

    /**
     * @var \Test\Classes\Comment[]
     */
    private $customComments;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var \Test\Classes\Number
     */
    private $number;
    /**
     * @var float
     */
    private $float;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param Comment[] $comments
     */
    public function setComments(array $comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return CustomCollection
     */
    public function getCustomComments(): CustomCollection
    {
        return $this->customComments;
    }

    /**
     * @param CustomCollection $customComments
     */
    public function setCustomComments(CustomCollection $customComments)
    {
        $this->customComments = $customComments;
    }


    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Number|null
     */
    public function getNumber(): ?Number
    {
        return $this->number;
    }

    /**
     * @param Number $number
     */
    public function setNumber(Number $number)
    {
        $this->number = $number;
    }

    /**
     * @return float
     */
    public function getFloat(): ?float
    {
        return $this->float;
    }

    /**
     * @param float $float
     */
    public function setFloat(float $float)
    {
        $this->float = $float;
    }



}