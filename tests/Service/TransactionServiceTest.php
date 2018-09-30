<?php

namespace Tests\Service;

use App\Model\Product;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\TransactionService;
use Tests\XmlTestCase;

class TransactionServiceTest extends XmlTestCase
{
    /**
     * @var TransactionService
     */
    private static $transactionService = null;

    public function testGetMovementsByWarehouse()
    {
        if (self::$transactionService === null) {
            self::$transactionService = new TransactionService(new TransactionRepository($this->dbal));
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
        $value = self::$transactionService->getMovementsByWarehouse(3);
        $this->assertEquals($value, $expectedValue);
        $value = self::$transactionService->getMovementsByWarehouse(8);
        $this->assertEquals($value, null);
    }

    public function testGetMovementsByProduct()
    {
        if (self::$transactionService === null) {
            self::$transactionService = new TransactionService(new TransactionRepository($this->dbal));
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
                'recipient' => '1',
            ],
            [
                'transactionId' => 13,
                'sku' => 115,
                'quantity' => 50,
                'cost' => 7500,
                'direction' => 'dispatch',
                'datetime' => '2018-09-01 13:20:39',
                'sender' => '1',
                'recipient' => 'Петров Иван Иванович'
            ]
        ];
        $value = self::$transactionService->getMovementsByProduct(4);
        $this->assertEquals($value, $expectedValue);
        $value = self::$transactionService->getMovementsByProduct(12);
        $this->assertEquals($value, null);
    }

    public function testAdd()
    {
        if (self::$transactionService === null) {
            self::$transactionService = new TransactionService(new TransactionRepository($this->dbal));
        }

        $product1 = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $product2 = new Product(
            3,
            125,
            'Морковь свежая',
            50,
            5,
            'food'
        );

        $transactions = [
            new Transaction(
                34,
                $product2,
                100,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '3'
            ),
            new Transaction(
                35,
                $product1,
                600,
                'dispatch',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            ),
            new Transaction(
                36,
                $product2,
                800,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            )
        ];
        $values = self::$transactionService->add($transactions);
        $this->assertEquals($values, $transactions);
    }
}