<?php

namespace App\Repository;

use App\Model\Product;
use App\Model\User;

class ProductRepository extends AbstractRepository
{
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
            $row['id'],
            $row['name'],
            $row['price'],
            $row['size'],
            $row['type']
        );
    }

    /**
     * @param Product $product
     * @param User $user
     */
    public function insert($product, $user)
    {
        $values = [
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'size' => $product->getSize(),
            'type' => $product->getType(),
            'userId' => $user->getId()
        ];

        $this->dbConnection->insert(
            'Products',
            $values
        );

        $product->setId($this->dbConnection->lastInsertId());
    }
    /**
     * @param $productId
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

        if($product->getName() !== null) {
            $values['name'] = $product->getName();
        }

        if($product->getPrice() !== null) {
            $values['price'] = $product->getPrice();
        }

        if($product->getSize() !== null) {
            $values['size'] = $product->getSize();
        }

        if($product->getType() !== null) {
            $values['type'] = $product->getType();
        }

        $this->dbConnection->update(
            'mvc.Products',
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