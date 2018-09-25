<?php

namespace Tests\Repository;

use App\Model\Product;
use App\Repository\ProductRepository;
use Tests\XmlTestCase;

class ProductRepositoryTest extends XmlTestCase
{
    /**
     * @var ProductRepository
     */
    static private $productRepository = null;

    /**
     * @dataProvider dataFindBySku
     */
    public function testFindBySku($sku, $userId, $expectedValues)
    {
        if (self::$productRepository === null) {
            self::$productRepository = new ProductRepository($this->dbal);
        }
        $product = self::$productRepository->findBySku($sku, $userId);
        $this->assertEquals($product, $expectedValues);
    }

    public function dataFindBySku()
    {
        $result = [];
        $result[] = [
            5555,
            1,
            new Product(
                1,
                5555,
                'Куриное филе охлажденное',
                250,
                3,
                'food'
            )
        ];
        $result[] = [
            111,
            1,
            null
        ];
        $result[] = [
            114,
            1,
            new Product(
                5,
                114,
                'Тапки резиновые с дырками',
                200,
                5,
                'household'
            )
        ];
        $result[] = [
            111,
            2,
            new Product(
                9,
                111,
                'Арабская поэзия средних веков',
                350,
                1,
                'book'
            )
        ];
        $result[] = [
            114,
            2,
            new Product(
                7,
                114,
                'Тапки резиновые с дырками',
                250,
                5,
                'household'
            )
        ];
        $result[] = [
            125,
            2,
            null
        ];
        $result[] = [
            5555,
            3,
            null
        ];
        return $result;
    }

    /**
     * @dataProvider dataFindById
     */
    public function testFindById($id, $expectedValues)
    {
        if (self::$productRepository === null) {
            self::$productRepository = new ProductRepository($this->dbal);
        }
        $product = self::$productRepository->findById($id);
        $this->assertEquals($product, $expectedValues);
    }

    public function dataFindById()
    {
        $result = [];
        $result[] = [
            1,
            new Product(
                1,
                5555,
                'Куриное филе охлажденное',
                250,
                3,
                'food'
            )
        ];
        $result[] = [
            111,
            null
        ];
        $result[] = [
            5,
            new Product(
                5,
                114,
                'Тапки резиновые с дырками',
                200,
                5,
                'household'
            )
        ];
        $result[] = [
            9,
            new Product(
                9,
                111,
                'Арабская поэзия средних веков',
                350,
                1,
                'book'
            )
        ];
        $result[] = [
            125,
            null
        ];
        return $result;
    }

    public function testDelete()
    {
        if (self::$productRepository === null) {
            self::$productRepository = new ProductRepository($this->dbal);
        }
        $value = self::$productRepository->delete(9);
        $expectedValue = self::$productRepository->findById(9);
        $this->assertEquals($value, $expectedValue);
    }

    /**
     * @dataProvider dataUpdate
     */
    public function testUpdate($product, $expectedValue)
    {
        if (self::$productRepository === null) {
            self::$productRepository = new ProductRepository($this->dbal);
        }
        $value = self::$productRepository->update($product);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataUpdate()
    {
        $result = [];
        $product = new Product(
            1,
            5555,
            'Куриное филе охлажденное',
            220,
            3,
            'food'
        );
        $result[] = [
            $product,
            $product
        ];
        $product = new Product(
                3,
                5555,
                'Морковь свежая мытая',
                220,
                3,
                'food'
        );
        $result[] = [
            $product,
            $product
        ];
        $product = new Product(
            5,
            7845,
            'Тапки резиновые с дырками',
            200,
            5,
            'household'
        );
        $result[] = [
            $product,
            $product
        ];
        $product = new Product(
            9,
            111,
            'Арабская поэзия средних веков',
            350,
            3,
            'book'
        );
        $result[] = [
            $product,
            $product
        ];
        $product = new Product(
            4,
            115,
            'Шампунь Xеден шолдерс',
            150,
            2,
            'chemistry'
        );
        $result[] = [
            $product,
            $product
        ];
        return $result;
    }

    public function testGetAll()
    {
        if (self::$productRepository === null) {
            self::$productRepository = new ProductRepository($this->dbal);
        }

        $values = self::$productRepository->getAll(2);
        $expectedValues[] = new Product(
            2,
            5555,
            'Куриное филе охлажденное',
            230,
            3,
            'food'
        );
        $expectedValues[] = new Product(
            7,
            114,
            'Тапки резиновые с дырками',
            250,
            5,
            'household'
        );
        $expectedValues[] = new Product(
            9,
            111,
            'Арабская поэзия средних веков',
            350,
            1,
            'book'
        );
        $expectedValues[] = new Product(
            10,
            5439	,
            'Героический эпос народов СССР',
            350,
            1,
            'book'
        );

        $this->assertEquals($values, $expectedValues);

        $values = self::$productRepository->getAll(5);
        $expectedValues = [];

        $this->assertEquals($values, $expectedValues);
    }

    public function testInsert()
    {
        if (self::$productRepository === null) {
            self::$productRepository = new ProductRepository($this->dbal);
        }
        $product = new Product(
            12,
            8800,
            'Кабель RCA',
            100,
            1,
            'household'
        );
        $value = self::$productRepository->insert($product, 1);
        $this->assertEquals($value, $product);
    }
}
