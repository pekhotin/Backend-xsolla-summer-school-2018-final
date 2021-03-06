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
     * @param Transaction[] $transactions
     *
     * @return Transaction[]
     */
    public function add($transactions)
    {
        return $this->transactionRepository->insert($transactions);
    }
    /**
     * @param int $warehouseId
     *
     * @return Transaction[]|null
     */
    public function getMovementsByWarehouse(int $warehouseId)
    {
        return $this->transactionRepository->findAllByWarehouse($warehouseId);
    }
    /**
     * @param int $productId
     *
     * @return Transaction[]|null
     */
    public function getMovementsByProduct($productId)
    {
        return $this->transactionRepository->getAllByProduct($productId);
    }
}
