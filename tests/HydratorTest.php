<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/25/17
 * Time: 11:14 PM
 */

class HydratorTest extends \PHPUnit\Framework\TestCase
{
    private function getUserData()
    {
        return [
            "id" => "123",
            "name" => "name_of_user",
            "email" => "email_of_user",
            "number" => "1",
            "float" => 10.42,
            "address" => ["city" => "test"],
            "comments" => [
                "test1" => [
                    "message" => "test_comment_message",
                    "date_time" => "2017-12-20"
                ],
                "test2" => [
                    "message" => "test_comment_message",
                    "date_time" => "2017-12-20"
                ]],
            "custom_comments" => [
                "test1" => [
                    "message" => "test_comment_message",
                    "date_time" => "2017-10-20"
                ],
                "test2" => [
                    "message" => "test_comment_message",
                    "date_time" => "2018-12-20"
                ]],
        ];
    }

    public function testSimpleHydration()
    {
        $hydrator = new \Clea\Hydrator\Hydrator();
        $data = $this->getUserData();
        unset($data["number"]);
        /**
         * @var \Test\Classes\User $user
         */
        $user = $hydrator->hydrate(\Test\Classes\User::class, $data);

        $this->assertEquals($this->getUserData()["id"], $user->getId());
        $this->assertNull($user->getNumber());
        $this->assertNull($user->getEmail());
        $this->assertEquals($this->getUserData()["address"]["city"], $user->getAddress()->getCity());
        $this->assertEquals($this->getUserData()["comments"]["test2"]["message"], $user->getComments()["test2"]->getMessage());
        $this->assertEquals($this->getUserData()["custom_comments"]["test1"]["message"], $user->getCustomComments()->first()->getMessage());
        $date = new \DateTime($this->getUserData()["custom_comments"]["test1"]["date_time"]);
        $this->assertEquals($date, $user->getCustomComments()->first()->getDateTime());

    }

    public function testCustomTransformHydration()
    {

        $hydrator = new \Clea\Hydrator\Hydrator([
            "collection_name" => "array",
            "additional_type" => [
                "float" => function ($value) {
                    return (float)$value;
                },
                \Test\Classes\Number::class => function ($value) {
                    return new \Test\Classes\Number($value);
                }
            ]
        ]);

        $user = $hydrator->hydrate(\Test\Classes\User::class, $this->getUserData());

        $this->assertEquals(new \Test\Classes\Number("1"), $user->getNumber());

        $this->assertEquals((float)10.42, $user->getFloat());
    }

    public function testExistingObject()
    {
        $hydrator = new Clea\Hydrator\Hydrator();

        $user = new \Test\Classes\User();
        $user->setName("test");
        $user->setEmail("test@test.com");
        $hydrator->hydrate($user, $this->getUserData());

        $this->assertEquals($this->getUserData()["name"], $user->getName());
        $this->assertEquals("test@test.com", $user->getEmail());
    }


    public function testPerformance()
    {

        $hydrator = new \Clea\Hydrator\Hydrator([
            "collection_name" => "array",
            "additional_type" => [
                "float" => function ($value) {
                    return (float)$value;
                }
            ]
        ]);
        PHP_Timer::start();
        for ($i = 1000; $i > 0; $i--) {
            $user = $hydrator->hydrate(\Test\Classes\User::class, $this->getUserData());
        }
        $time = PHP_Timer::stop() * 1000;

        $this->assertLessThan(200, $time);

    }

}