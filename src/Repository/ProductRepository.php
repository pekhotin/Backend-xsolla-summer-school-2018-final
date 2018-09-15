<?php

namespace App\Repository;

use App\Model\Product;
use App\Model\User;

class ProductRepository extends AbstractRepository
{
    /**
     * @param int $sku
     * @param User $user
     *
     * @return Product|null
     */
    public function findBySku($sku, $user)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Products WHERE sku = ? AND userId = ?',
            [$sku, $user->getId()]
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
     * @param User $user
     *
     * @return Product|null
     */
    public function findById($id, $user)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Products WHERE id = ? AND userId = ?',
            [$id, $user->getId()]
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
     * @param User $user
     */
    public function insert($product, $user)
    {
        $values = $product->getProductArray();
        $values['userId'] = $user->getId();

        $this->dbConnection->insert(
            'Products',
            $values
        );

        $product->setId($this->dbConnection->lastInsertId());
    }
    /**
     * @param int $productId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($productId)
    {
        $this->dbConnection->delete(
            'Products',
            ['id' => $productId]
        );
    }
    /**
     * @param Product $product
     * @param User $user
     *
     * @return Product|null
     */
    public function update($product, $user)
    {
        $values = [];

        if ($product->getSku() !== null) {
            $values['sku'] = $product->getSku();
        }
        if ($product->getName() !== null) {
            $values['name'] = $product->getName();
        }

        if ($product->getPrice() !== null) {
            $values['price'] = $product->getPrice();
        }

        if ($product->getSize() !== null) {
            $values['size'] = $product->getSize();
        }

        if ($product->getType() !== null) {
            $values['type'] = $product->getType();
        }

        $this->dbConnection->update(
            'Products',
            $values,
            [
                'id' => $product->getId(),
                'userId' => $user->getId()
            ]
        );

        return $this->findById($product->getId(), $user);
    }
    /**
     * @param User $user
     *
     * @return Product[]
     */
    public function getAll($user)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT * FROM Products WHERE userId = ?',
            [$user->getId()]
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