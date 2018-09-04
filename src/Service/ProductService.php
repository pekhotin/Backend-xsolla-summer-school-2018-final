<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Model\Product;

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
     * @return Product[]
     */
    public function getAll()
    {
        return $this->productRepository->getAll();
    }

    /**
     * @param int $id
     *
     * @return Product|null
     */
    public function getOne($id)
    {
        return $this->productRepository->findById($id);
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    public function add(Product $product)
    {
        return $this->productRepository->insert($product);
    }

    /**
     * @param Product $product
     * @return Product
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function remove(Product $product)
    {
        return $this->productRepository->delete($product);
    }

    /**
     * @param Product $product
     *
     * @return Product
     */
    public function update(Product $product)
    {
        return $this->productRepository->update($product);
    }
}