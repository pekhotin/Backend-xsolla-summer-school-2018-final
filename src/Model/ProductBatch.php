<?php

namespace App\Model;

class ProductBatch
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * ProductBatch constructor.
     *
     * @param int $id
     * @param Product $product
     * @param int $quantity
     */
    public function __construct($id, $product, $quantity)
    {
        $this->id = $id;
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return array
     */
    public function getProductBatchArray()
    {
        $productBatch = $this->product->getProductArray();
        $productBatch['quantity'] = $this->quantity;

        return $productBatch;
    }

    public function getSize()
    {
        return $this->product->getSize() * $this->quantity;
    }
}