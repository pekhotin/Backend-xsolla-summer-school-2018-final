<?php

namespace App\Model;

class Transaction
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
     * @param Product $product
     * @param int $quantity
     * @param string $direction
     * @param string $datetime
     * @param string $sender
     * @param string $recipient
     */
    public function __construct($id, $product, $quantity, $direction, $datetime, $sender, $recipient)
    {
        $this->id = $id;
        $this->product = $product;
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
    /**
     * @param string $datetime
     */
    public function setDatetime($datetime)
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
            'productId' => $this->product->getId(),
            'quantity' => $this->quantity,
            'direction' => $this->direction,
            'datetime' => $this->datetime,
            'sender' => $this->sender,
            'recipient' => $this->recipient
        ];
    }
    /**
     * @return array
     */
    public function getTransactionInfo()
    {
        return [
            'transactionId' => $this->id,
            'sku' => $this->product->getSku(),
            'quantity' => $this->quantity,
            'direction' => $this->direction,
            'datetime' => $this->datetime,
            'sender' => $this->sender,
            'recipient' => $this->recipient
        ];
    }
}
