<?php

namespace App\Service;

use App\Model\Transaction;
use App\Repository\TransactionRepository;

class TransactionService
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * TransactionService constructor.
     *
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository) {

        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param Transaction $transaction
     */
    public function add($transaction)
    {
        $this->transactionRepository->insert($transaction);
    }

    /**
     * @param int $warehouseId
     *
     * @return Transaction[]|null
     */
    public function findByWarehouseId($warehouseId)
    {
        return $this->transactionRepository->findAllByWarehouseId($warehouseId);
    }

}