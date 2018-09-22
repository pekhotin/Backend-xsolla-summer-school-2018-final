<?php

namespace Tests;

use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;

class XmlTestCase extends AbstractDatabaseTestCase
{
    protected function getDataSet()
    {
        return new FlatXmlDataSet(__DIR__. '/fixtures/users.xml');
        //$this->createFlatXMLDataSet('fixtures/users.xml');
    }
}