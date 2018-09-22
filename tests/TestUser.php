<?php

namespace Tests;


class TestUser extends XmlTestCase
{
    //здесь надо делать функциональные тесты
    public function testUsers()
    {
        $sql = "SELECT * FROM `mvc`.`Users`";
        $statement =
            $this->getConnection()->getConnection()->query($sql);
        $result = $statement->fetchAll();
        $this->assertEquals(2, sizeof($result));
        //$this->assertEquals('Nalabal', $result[00]['name']);
    }
}