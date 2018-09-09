<?php


namespace App\Model;


class Transaction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $warehouseId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var string
     */
    private $datetime;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var string
     */
    private $recipient;

    /**
     * Transaction constructor.
     *
     * @param int $id
     * @param int $warehouseId
     * @param int $productId
     * @param int $quantity
     * @param string $direction
     * @param string $datetime
     * @param string $sender
     * @param string $recipient
     */
    public function __construct($id, $warehouseId, $productId, $quantity, $direction, $datetime, $sender, $recipient)
    {
        $this->id = $id;
        $this->warehouseId = $warehouseId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->direction = $direction;
        $this->datetime = $datetime;
        $this->sender = $sender;
        $this->recipient = $recipient;
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
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
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
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDate($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return array
     */
    public function getTransactionArray()
    {
        return [
            'warehouseId' => $this->warehouseId,
            'productId' => $this->productId,
            'quantity' => $this->quantity,
            'direction' => $this->direction,
            'datetime' => $this->datetime,
            'sender' => $this->sender,
            'recipient' => $this->recipient
        ];
    }

}