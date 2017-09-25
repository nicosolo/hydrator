<?php
/**
 * Created by IntelliJ IDEA.
 * User: nico
 * Date: 9/25/17
 * Time: 11:14 PM
 */

class HydratorTest extends \PHPUnit\Framework\TestCase
{
    private function getUserData(){
        return [
            "id" => "id_test",
            "name" => "name_of_user",
            "email" => "email_of_user",
            "address" => ["city" => "test"],
            "comments" => [ "ttest" => [
                "message" => "test_comment_message",
                "date_time" => "2017-12-20"
            ]]
        ];
    }

    public function testSimpleHydration(){
        $hydrator = new \Clea\Hydrator\Hydrator();
        $users = [];
        for ($i = 1; $i > 0; $i--) {
            $users[] = $hydrator->hydrate(\Test\Classes\User::class, $this->getUserData());
        }

        var_dump($users);

    }

}