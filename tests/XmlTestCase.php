<?php

namespace Tests;

class XmlTestCase extends AbstractDatabaseTestCase
{
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/fixtures/dataset.xml');
    }
}