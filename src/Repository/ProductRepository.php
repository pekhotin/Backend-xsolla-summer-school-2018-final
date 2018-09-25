<?php

namespace App\Repository;

use App\Model\Product;

class ProductRepository extends AbstractRepository
{
    /**
     * @param int $sku
     * @param int $userId
     *
     * @return Product|null
     */
    public function findBySku($sku, $userId)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Products WHERE sku = ? AND userId = ?',
            [$sku, $userId]
        );

        if ($row === false) {
            return null;
        }

        return new Product(
            (int)$row['id'],
            (int)$row['sku'],
            (string)$row['name'],
            (float)$row['price'],
            (int)$row['size'],
            (string)$row['type']
        );
    }
    /**
     * @param int $id
     *
     * @return Product|null
     */
    public function findById($id)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Products WHERE id = ?',
            [$id]
        );

        if ($row === false) {
            return null;
        }

        return new Product(
            (int)$row['id'],
            (int)$row['sku'],
            (string)$row['name'],
            (float)$row['price'],
            (int)$row['size'],
            (string)$row['type']
        );
    }
    /**
     * @param Product $product
     * @param int $userId
     *
     * @return Product
     */
    public function insert($product, $userId)
    {
        $values = $product->getProductArray();
        $values['userId'] = $userId;

        $this->dbConnection->insert(
            'Products',
            $values
        );

        $product->setId((int)$this->dbConnection->lastInsertId());
        return $product;
    }
    /**
     * @param int $productId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     *
     * @return null
     */
    public function delete($productId)
    {
        $this->dbConnection->delete(
            'Products',
            ['id' => $productId]
        );

        return null;
    }
    /**
     * @param Product $product
     *
     * @return Product|null
     */
    public function update($product)
    {
        $this->dbConnection->update(
            'Products',
            $product->getProductArray(),
            [
                'id' => $product->getId()
            ]
        );

        return $this->findById($product->getId());
    }
    /**
     * @param int $userId
     *
     * @return Product[]
     */
    public function getAll($userId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT * FROM Products WHERE userId = ?',
            [$userId]
        );

        $products = [];

        foreach ($rows as $row) {
            $products[] = new Product(
                (int)$row['id'],
                (int)$row['sku'],
                (string)$row['name'],
                (float)$row['price'],
                (int)$row['size'],
                (string)$row['type']
            );
        }

        return $products;
    }
}