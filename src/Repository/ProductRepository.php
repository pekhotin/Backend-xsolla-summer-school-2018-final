<?php

namespace App\Repository;

use App\Model\Product;
use Doctrine\DBAL\Connection;

class ProductRepository extends AbstractRepository
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * ProductRepository constructor.
     *
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
        $this->tableName = 'Products';
    }

    /**
     * @param int $id
     *
     * @return Product
     *
     * @throws \LogicException
     */
    public function findById($id)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM ' . $this->tableName . ' WHERE id = ?',
            [$id]
        );

        if ($row == null) {
            throw new \LogicException(__CLASS__ . ' findById () product with id ' . $id . ' not found!', 404);
        }

        return new Product(
            $row['id'],
            $row['name'],
            $row['price'],
            $row['size'],
            $row['type']
        );
    }

    /**
     * @param Product $product
     *
     * @return Product
     *
     * @throws \LogicException
     */
    public function insert(Product $product)
    {
        $values = [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'size' => $product->getSize(),
            'type' => $product->getType()
        ];

        $this->dbConnection->insert(
            $this->tableName,
            $values
        );

        $product->setId($this->dbConnection->lastInsertId());

        return $product;
    }

    /**
     * @param Product $product
     *
     * @return Product
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \LogicException
     */
    public function delete(Product $product)
    {
        //проверка участвовал ли продукт в перемещениях

        $this->dbConnection->delete(
            $this->tableName,
            ['id' => $product->getId()]
        );

        return $product;
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    public function update(Product $product)
    {
        $values = [];

        if($product->getName() !== null) {
            $values['name'] = $product->getName();
        }

        if($product->getPrice() !== null) {
            $values['price'] = $product->getPrice();
        }

        if($product->getSize() !== null) {
            //участвовал ли продукт в перемещениях
            $values['size'] = $product->getSize();
        }

        if($product->getType() !== null) {
            $values['type'] = $product->getType();
        }

        if(count($values) == 0) {
            throw new \LogicException(__CLASS__ . " update() enter parameters you want to update!");
        }

        $this->dbConnection->update(
            $this->tableName,
            $values,
            ['id' => $product->getId()]
        );

        return $this->findById($product->getId());
    }

    /**
     * @return Product[]
     */
    public function getAll()
    {
        $rows = $this->dbConnection->fetchAll('SELECT * FROM ' . $this->tableName);

        //здесь будет проверка

        $products = [];

        foreach ($rows as $row) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['size'],
                $row['type']
            );
        }

        return $products;
    }
}