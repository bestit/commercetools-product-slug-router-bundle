<?php

namespace BestIt\CtProductSlugRouter\Router;

use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouter\Repository\ProductRepositoryInterface;
use Commercetools\Core\Model\Product\ProductProjection;
use InvalidArgumentException;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Interface ProductRepositoryInterface
 * @author despendiller <espendiller@bestit-online.de>
 * @package BestIt\CtProductSlugRouter
 * @subpackage Router
 * @version $id$
 */
class ProductRouter implements RouterInterface, VersatileGeneratorInterface
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
     * The request context
     * @var RequestContext
     */
    private $context;

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
     * @inheritdoc
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        if ($referenceType != self::ABSOLUTE_PATH) {
            throw new RouteNotFoundException('Only `absolute path` is allowed for product route generation');
        }

        if (is_string($name)) {
            $slug = $this->getSlugByName($name, $parameters);
        } else {
            $slug = $this->getSlugByObject($name);
        }

        if (!$slug) {
            throw new RouteNotFoundException('Not product found for route ' . (string)$name);
        }

        $url = sprintf('%s/%s', $this->getContext()->getBaseUrl(), $slug);
        if ($query = http_build_query($parameters)) {
            $url .= sprintf('?%s', $query);
        }

        return $url;
    }

    /**
     * Get product slug by name
     * @param string $name
     * @param array $params
     * @return string|null
     */
    private function getSlugByName(string $name, array &$params)
    {
        $slug = null;

        if ($name === $this->getRoute()) {
            if (array_key_exists('slug', $params)) {
                $slug = $params['slug'];
                unset($params['slug']); // we do not want to add this to query
            } else {
                throw new InvalidArgumentException('Missing param `slug` for product route generation');
            }
        }

        return $slug;
    }

    /**
     * Get product slug by object
     * @param $object
     * @return null|string
     */
    private function getSlugByObject($object)
    {
        $slug = null;

        if ($object instanceof ProductProjection) {
            $slug = ($value = $object->getSlug()) ? $value->getLocalized() : null;
        }

        return $slug;
    }

    /**
     * Gets the request context.
     * @return RequestContext The context
     */
    public function getContext(): RequestContext
    {
        return $this->context;
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
     * Gets the RouteCollection instance associated with this Router.
     * @return RouteCollection A RouteCollection instance
     * @todo Implement.
     */
    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    /**
     * @inheritdoc
     */
    public function getRouteDebugMessage($name, array $parameters = [])
    {
        return (string)$name;
    }

    /**
     * Tries to match a URL path with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @param string $pathinfo The path info to be parsed (raw format, i.e. not urldecoded)
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     */
    public function match($pathInfo): array
    {
        try {
            $product = $this->getRepository()->getProductBySlug(trim($pathInfo, '/'));
        } catch (ProductNotFoundException $e) {
            throw new ResourceNotFoundException('Not product found for slug ' . $pathInfo);
        }

        return [
            '_controller' => $this->getController(),
            '_route' => $this->getRoute(),
            'product' => $product,
        ];
    }

    /**
     * Sets the request context.
     * @param RequestContext $context The context
     * @return ProductRouter
     */
    public function setContext(RequestContext $context): ProductRouter
    {
        $this->context = $context;

        return $this;
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
     * @inheritdoc
     */
    public function supports($name)
    {
        return $name instanceof ProductProjection || $name == $this->getRoute();
    }
}
