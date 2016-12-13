<?php

namespace BestIt\CtProductSlugRouter\Repository;

use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;

interface ProductRepositoryInterface
{
    /**
     * Get product by slug
     * @param string $slug
     * @return mixed
     * @throws ProductNotFoundException
     */
    public function getProductBySlug(string $slug);
}
