<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/25/17
 * Time: 11:07 PM
 */

namespace Test\Classes;


class Comment
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var User
     */
    private $user;

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }





}