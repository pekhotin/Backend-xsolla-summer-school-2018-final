<?php

namespace App\Model;

class Product
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $sku;
    /**
     * @var string
     */
    private $name;
    /**
     * @var float
     */
    private $price;
    /**
     * @var int
     */
    private $size;
    /**
     * @var string
     */
    private $type;
    /**
     * Product constructor.
     *
     * @param int $id
     * @param string $name
     * @param float $price
     * @param int $size
     * @param string $type
     */
    public function __construct($id, $sku, $name, $price, $size, $type)
    {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->size = $size;
        $this->type = $type;
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
     * @return int
     */
    public function getSku()
    {
        return $this->sku;
    }
    /**
     * @param int $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }
    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    /**
     * @return array
     */
    public function getProductArray()
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'size' => $this->size,
            'type' => $this->type
        ];
    }
}