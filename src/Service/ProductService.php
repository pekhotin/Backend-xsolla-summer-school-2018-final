<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Model\Product;
use App\Model\User;

class ProductService
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * ProductService constructor.
     * 
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository) {
        
        $this->productRepository = $productRepository;
    }
    /**
     * @param User $user
     *
     * @return Product[]
     */
    public function getAll(User $user)
    {
        return $this->productRepository->getAll($user->getId());
    }

    /**
     * @param int $sku
     * @param int $userId
     *
     * @return Product|null
     */
    public function getOneBySku(int $sku, int $userId)
    {
        return $this->productRepository->findBySku($sku, $userId);
    }
    /**
     * @param Product $product
     * @param User $user
     *
     * @return Product
     */
    public function add(Product $product, User $user)
    {
        return $this->productRepository->insert($product, $user->getId());
    }
    /**
     * @param int $productId
     *
     * @return null
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function remove(int $productId)
    {
        return $this->productRepository->delete($productId);
    }
    /**
     * @param Product $product
     *
     * @return Product|null
     */
    public function update(Product $product)
    {
        return $this->productRepository->update($product);
    }
}
