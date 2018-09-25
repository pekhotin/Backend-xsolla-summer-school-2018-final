<?php

namespace Tests;

class TestUser extends XmlTestCase
{
    public function testUsers()
    {
        $sql = "SELECT * FROM Transactions";
        $statement =
            $this->getConnection()->getConnection()->query($sql);
        $result = $statement->fetchAll();
        $this->assertEquals(31, sizeof($result));
    }
}