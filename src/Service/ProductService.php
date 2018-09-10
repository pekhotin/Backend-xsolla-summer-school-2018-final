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
        return $this->productRepository->getAll($user);
    }

    /**
     * @param $id
     * @param User $user
     *
     * @return Product|null
     */
    public function getOne($id, User $user)
    {
        return $this->productRepository->findById($id, $user);
    }

    /**
     * @param Product $product
     * @param User $user
     */
    public function add(Product $product, User $user)
    {
        return $this->productRepository->insert($product, $user);
    }

    /**
     * @param $productId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function remove($productId)
    {
        return $this->productRepository->delete($productId);
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    public function update(Product $product, User $user)
    {
        return $this->productRepository->update($product, $user);
    }
}