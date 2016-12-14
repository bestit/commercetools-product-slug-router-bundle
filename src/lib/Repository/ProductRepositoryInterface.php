<?php

namespace BestIt\CtProductSlugRouter\Repository;

use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;

/**
 * Repository to get products with a slug.
 * @author lange <lange@bestit-online.de>
 * @package BestIt\CtProductSlugRouter
 * @subpackage Repository
 * @version $id$
 */
interface ProductRepositoryInterface
{
    /**
     * Get product by slug
     * @param string $slug
     * @param bool $exceptionOnMiss Should an exception be thrown if the product is not found.
     * @return mixed
     * @throws ProductNotFoundException
     */
    public function getProductBySlug(string $slug, bool $exceptionOnMiss = true);
}
