<?php

namespace BestIt\CtProductSlugRouterBundle\Router;

use BestIt\Core\CoreBundle\Router\RouterInterface;
use BestIt\CtProductSlugRouterBundle\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouterBundle\Repository\ProductRepositoryInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Interface ProductRepositoryInterface
 * @author despendiller <espendiller@bestit-online.de>
 * @package BestIt\CtProductSlugRouterBundle
 * @subpackage Router
 * @version $id$
 */
class ProductRouter implements RouterInterface
{
    /**
     * The default controller.
     * @var string
     */
    const DEFAULT_CONTROLLER = 'BestIt\Frontend\ProductBundle\Controller\DetailController::indexAction';

    /**
     * The default route.
     * @var string
     */
    const DEFAULT_ROUTE = 'best_it_frontend_product_detail_index';

    /**
     * The logical/full name for the used controller.
     * @var string
     */
    private $controller = '';

    /**
     * The repository to fetch products by slug.
     * @var ProductRepositoryInterface
     */
    private $repository;

    /**
     * The used route name for this router.
     * @var string
     */
    private $route = '';

    /**
     * ProductRouter constructor.
     * @param ProductRepositoryInterface $repository
     * @param string $controller
     * @param string $route
     */
    public function __construct(
        ProductRepositoryInterface $repository,
        string $controller = self::DEFAULT_CONTROLLER,
        $route = self::DEFAULT_ROUTE
    ) {
        $this
            ->setController($controller)
            ->setRepository($repository)
            ->setRoute($route);
    }

    /**
     * Returns the logical/full name for the used controller.
     * @return string
     */
    private function getController(): string
    {
        return $this->controller;
    }

    /**
     * Returns the repository to fetch products by slug.
     * @return ProductRepositoryInterface
     */
    private function getRepository(): ProductRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Returns the used route name for this router.
     * @return string
     */
    private function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Sets the logical/full name for the used controller.
     * @param string $controller
     * @return ProductRouter
     */
    private function setController(string $controller): ProductRouter
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Sets the repository to fetch products by slug.
     * @param ProductRepositoryInterface $repository
     * @return ProductRouter
     */
    private function setRepository(ProductRepositoryInterface $repository): ProductRouter
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Sets the used route name for this router.
     * @param string $route
     * @return ProductRouter
     */
    private function setRoute(string $route): ProductRouter
    {
        $this->route = $route;
        return $this;
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
    ) : string
    {
        throw new RouteNotFoundException('Not supported by ProductRouter');
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo) : array
    {
        try {
            $product = $this->getRepository()->getProductBySlug(trim($pathinfo, '/'));
        } catch (ProductNotFoundException $e) {
            throw new ResourceNotFoundException('Not product found for ProductRouter');
        }

        return [
            '_controller' => $this->getController(),
            '_route' => $this->getRoute(),
            'product' => $product,
        ];
    }
}
