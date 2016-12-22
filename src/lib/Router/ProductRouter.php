<?php

namespace BestIt\CtProductSlugRouter\Router;

use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouter\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
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
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * Parameters that reference placeholders in the route pattern will substitute them in the
     * path or host. Extra params are added as query string to the URL.
     *
     * When the passed reference type cannot be generated for the route because it requires a different
     * host or scheme than the current one, the method will return a more comprehensive reference
     * that includes the required params. For example, when you call this method with $referenceType = ABSOLUTE_PATH
     * but the route requires the https scheme whereas the current scheme is http, it will instead return an
     * ABSOLUTE_URL with the https scheme and the current host. This makes sure the generated URL matches
     * the route in any case.
     *
     * If there is no route with the given name, the generator must throw the RouteNotFoundException.
     *
     * @param string $name The name of the route
     * @param mixed $parameters An array of parameters
     * @param int $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     * @todo Implement.
     * @throws RouteNotFoundException              If the named route doesn't exist
     * @throws MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                             it does not match the requirement
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        throw new RouteNotFoundException('Not supported by ProductRouter');
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
}
