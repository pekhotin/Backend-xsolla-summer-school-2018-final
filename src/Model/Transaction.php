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
     * @param int $productId
     * @param int $quantity
     * @param string $direction
     * @param string $datetime
     * @param string $sender
     * @param string $recipient
     */
    public function __construct($id, $productId, $quantity, $direction, $datetime, $sender, $recipient)
    {
        $this->id = $id;
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
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }
    /**
     * @param int $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
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
            'productId' => $this->productId,
            'quantity' => $this->quantity,
            'direction' => $this->direction,
            'datetime' => $this->datetime,
            'sender' => $this->sender,
            'recipient' => $this->recipient
        ];
    }

    public function getTransactionInfo()
    {
        return [
            'transactionId' => $this->id,
            'productId' => $this->productId,
            'quantity' => $this->quantity,
            'direction' => $this->direction,
            'datetime' => $this->datetime,
            'sender' => $this->sender,
            'recipient' => $this->recipient
        ];
    }
}