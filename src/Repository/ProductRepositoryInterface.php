<?php

namespace BestIt\CtProductSlugRouter\Repository;

interface ProductRepositoryInterface
{
    /**
     * Get product by slug
     * @param string $slug
     * @return Product
     * @throws ProductNotFoundException
     */
    public function getProductBySlug(string $slug) : Product;
}
