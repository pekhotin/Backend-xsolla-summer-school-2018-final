<?php

namespace Tests\Repository;

use App\Repository\TransactionRepository;
use Tests\XmlTestCase;

class TransactionRepositoryTest extends XmlTestCase
{
    /**
     * @var TransactionRepository
     */
    private static $transactionRepository = null;

    public function testFindAllByWarehouse()
    {
        if (self::$transactionRepository === null) {
            self::$transactionRepository = new TransactionRepository($this->dbal);
        }

        $expectedValue = [
            [
                'transactionId' => 10,
                'sku' => 125,
                'quantity' => 500,
                'cost' => 25000,
                'direction' => 'receipt',
                'datetime' => '2018-09-01 13:07:49',
                'sender' => 'Петров Иван Иванович',
                'recipient' => 3,
            ],
            [
                'transactionId' => 17,
                'sku' => 114,
                'quantity' => 20,
                'cost' => 4000,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-01 13:34:07',
                'sender' => 1,
                'recipient' => 3
            ],
            [
                'transactionId' => 27,
                'sku' => 785,
                'quantity' => 50,
                'cost' => 2500,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-02 13:55:11',
                'sender' => 4,
                'recipient' => 3,
            ],
            [
                'transactionId' => 28,
                'sku' => 114,
                'quantity' => 20,
                'cost' => 4000,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-02 13:55:55',
                'sender' => 3,
                'recipient' => 1,
            ],
            [
                'transactionId' => 29,
                'sku' => 785,
                'quantity' => 100,
                'cost' => 5000,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-02 13:56:41',
                'sender' => 2,
                'recipient' => 3,
            ]
        ];
        $value = self::$transactionRepository->findAllByWarehouse(3);
        $this->assertEquals($value, $expectedValue);
        $value = self::$transactionRepository->findAllByWarehouse(8);
        $this->assertEquals($value, []);
    }

    public function testGetAllByProduct()
    {
        if (self::$transactionRepository === null) {
            self::$transactionRepository = new TransactionRepository($this->dbal);
        }

        $expectedValue = [
            [
                'transactionId' => 1,
                'sku' => 115,
                'quantity' => 50,
                'cost' => 7500,
                'direction' => 'receipt',
                'datetime' => '2018-09-01 13:05:45',
                'sender' => 'Пазолини Корней Свястоплясович',
                'recipient' => 3,
            ],
            [
                'transactionId' => 17,
                'sku' => 114,
                'quantity' => 20,
                'cost' => 4000,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-01 13:34:07',
                'sender' => 1,
                'recipient' => 3
            ],
            [
                'transactionId' => 27,
                'sku' => 785,
                'quantity' => 50,
                'cost' => 2500,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-02 13:55:11',
                'sender' => 4,
                'recipient' => 3,
            ],
            [
                'transactionId' => 28,
                'sku' => 114,
                'quantity' => 20,
                'cost' => 4000,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-02 13:55:55',
                'sender' => 3,
                'recipient' => 1,
            ],
            [
                'transactionId' => 29,
                'sku' => 785,
                'quantity' => 100,
                'cost' => 5000,
                'direction' => 'betweenWarehouses',
                'datetime' => '2018-09-02 13:56:41',
                'sender' => 2,
                'recipient' => 3,
            ]
        ];
        $value = self::$transactionRepository->getAllByProduct(4);
        $this->assertEquals($value, $expectedValue);
    }
}