<?php

namespace BestIt\CtProductSlugRouterBundle\Repository;

use BestIt\CtProductSlugRouterBundle\Exception\ProductNotFoundException;

/**
 * Repository to get products with a slug.
 * @author lange <lange@bestit-online.de>
 * @package BestIt\CtProductSlugRouterBundle
 * @subpackage Repository
 * @version $id$
 */
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
