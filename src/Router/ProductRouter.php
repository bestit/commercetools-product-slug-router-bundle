<?php

namespace BestIt\CtProductSlugRouter\Router;

use BestIt\Core\CoreBundle\Router\RouterInterface;
use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouter\Repository\ProductRepositoryInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductRouter implements RouterInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $repository;

    /**
     * ProductRouter constructor.
     *
     * @param ProductRepositoryInterface $repository
     */
    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($mixed) : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function generate(
        $name,
        $parameters = array(),
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) : string {
        throw new RouteNotFoundException('Not supported by ProductRouter');
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo) : array
    {
        try {
            $product = $this->repository->getProductBySlug(trim($pathinfo, '/'));
        } catch (ProductNotFoundException $e) {
            throw new ResourceNotFoundException('Not product found for ProductRouter');
        }

        return [
            '_controller' => 'BestIt\Frontend\ProductBundle\Controller\DetailController::indexAction',
            '_route' => 'best_it_frontend_product_detail_index',
            'product' => $product,
        ];
    }
}
