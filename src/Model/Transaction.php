<?php


namespace App\Model;


class Transaction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Warehouse
     */
    private $warehouse;

    /**
     * @var Product
     */
    private $product;

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
    private $date;

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
     * @param Warehouse $warehouse
     * @param Product $product
     * @param int $quantity
     * @param string $direction
     * @param string $date
     * @param string $sender
     * @param string $recipient
     */
    public function __construct($id, $warehouse, $product, $quantity, $direction, $date, $sender, $recipient)
    {
        $this->id = $id;
        $this->warehouse = $warehouse;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->direction = $direction;
        $this->date = $date;
        $this->recipient = $recipient;
        $this->sender = $sender;
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
    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
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
            'idWarehouse' => $this->warehouse->getId(),
            'idProduct' => $this->product->getId(),
            'quantity' => $this->quantity,
            'direction' => $this->direction,
            'date' => $this->date,
            'sender' => $this->sender,
            'recipient' => $this->recipient
        ];
    }

}