<?php

namespace App\Model;

class Warehouse
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $address;
    /**
     * @var int
     */
    private $capacity;
    /**
     * Warehouse constructor.
     *
     * @param $id
     * @param $address
     * @param $capacity
     */
    public function __construct($id, $address, $capacity)
    {
        $this->id = $id;
        $this->address = $address;
        $this->capacity = $capacity;
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
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
    /**
     * @return int
     */
    public function getCapacity()
    {
        return $this->capacity;
    }
    /**
     * @param int $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }
    /**
     * @return array
     */
    public function getWarehouseArray()
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'capacity' => $this->capacity
        ];
    }
}
