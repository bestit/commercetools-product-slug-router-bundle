<?php

namespace BestIt\CtProductSlugRouter\Router;

use BestIt\CtProductSlugRouter\Exception\ForbiddenCharsException;
use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouter\Repository\ProductRepositoryInterface;
use Commercetools\Core\Model\Product\ProductProjection;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use function get_class;
use function json_encode;

/**
 * Interface ProductRepositoryInterface
 *
 * @author despendiller <espendiller@bestit-online.de>
 * @package BestIt\CtProductSlugRouter\Router
 */
class ProductRouter implements LoggerAwareInterface, RouterInterface, VersatileGeneratorInterface
{
    use LoggerAwareTrait;

    /**
     * The default controller.
     *
     * @var string
     */
    const DEFAULT_CONTROLLER = 'BestIt\Frontend\ProductBundle\Controller\DetailController::indexAction';

    /**
     * The default route.
     *
     * @var string
     */
    const DEFAULT_ROUTE = 'best_it_frontend_product_detail_index';

    /**
     * Any character not included in this expression is considered a forbidden character.
     *
     * @var string
     */
    const FORBIDDEN_CHARS_REGEX = '/[^-a-zA-Z0-9_\/]/';
    /**
     * The request context
     *
     * @var RequestContext
     */
    private $context;
    /**
     * The logical/full name for the used controller
     *
     * @var string
     */
    private $controller = '';
    /**
     * The repository to fetch products by slug.
     *
     * @var ProductRepositoryInterface
     */
    private $repository;
    /**
     * The used route name for this router.
     *
     * @var string
     */
    private $route = '';

    /**
     * ProductRouter constructor.
     *
     * @param ProductRepositoryInterface $repository
     * @param string $controller
     * @param string $route
     */
    public function __construct(
        ProductRepositoryInterface $repository,
        string $controller = self::DEFAULT_CONTROLLER,
        string $route = self::DEFAULT_ROUTE
    ) {
        $this
            ->setController($controller)
            ->setRepository($repository)
            ->setRoute($route);

        $this->logger = new NullLogger();
    }

    /**
     * Generates a url for the given parameters.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string $name
     * @param array $parameters
     * @param int $referenceType
     *
     * @return string
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
            throw new RouteNotFoundException('Not product found for route ' . (string) $name);
        }

        $url = sprintf('%s/%s', $this->getContext()->getBaseUrl(), $slug);
        if ($query = http_build_query($parameters)) {
            $url .= sprintf('?%s', $query);
        }

        return $url;
    }

    /**
     * Gets the request context.
     *
     * @return RequestContext The context
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * Returns the logical/full name for the used controller.
     *
     * @return string
     */
    private function getController(): string
    {
        return $this->controller;
    }

    /**
     * Returns the repository to fetch products by slug.
     *
     * @return ProductRepositoryInterface
     */
    private function getRepository(): ProductRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Returns the used route name for this router.
     *
     * @return string
     */
    private function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     *
     * @todo Implement.
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    /**
     * Returns the route debug message.
     *
     * @param mixed $name
     * @param array $parameters
     *
     * @return string
     */
    public function getRouteDebugMessage($name, array $parameters = [])
    {
        return sprintf(
            '%s:%s(%s)',
            get_class($this),
            $this->isValidObject($name) ? $name->getId() : $name,
            json_encode($parameters)
        );
    }

    /**
     * Get product slug by name.
     *
     * @param string $name
     * @param array $params
     *
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
                $this->logger->critical(
                    'Missing param `slug` for product route generation.',
                    [
                        'name' => $name,
                        'params' => $params
                    ]
                );

                throw new InvalidArgumentException('Missing param `slug` for product route generation.');
            }
        }

        return $slug;
    }

    /**
     * Get product slug by object.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param mixed $object
     *
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
     * Is the parameter a usable object?
     *
     * @param string|ProductProjection $name
     *
     * @return bool
     */
    private function isValidObject($name): bool
    {
        return $name instanceof ProductProjection;
    }

    /**
     * Tries to match a URL path with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param string $pathInfo The path info to be parsed (raw format, i.e. not urldecoded)
     *
     * @throws ForbiddenCharsException If pathInfo contains special characters
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     *
     * @return array An array of parameters
     */
    public function match($pathInfo): array
    {
        try {
            if (preg_match(self::FORBIDDEN_CHARS_REGEX, $pathInfo)) {
                throw new ForbiddenCharsException($pathInfo . ' is not allowed to have special characters!');
            }

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
     *
     * @param RequestContext $context The context
     *
     * @return ProductRouter
     */
    public function setContext(RequestContext $context): ProductRouter
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Sets the logical/full name for the used controller.
     *
     * @param string $controller
     *
     * @return ProductRouter
     */
    private function setController(string $controller): ProductRouter
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Sets the repository to fetch products by slug.
     *
     * @param ProductRepositoryInterface $repository
     *
     * @return ProductRouter
     */
    private function setRepository(ProductRepositoryInterface $repository): ProductRouter
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Sets the used route name for this router.
     *
     * @param string $route
     *
     * @return ProductRouter
     */
    private function setRoute(string $route): ProductRouter
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Does this router support the given object?
     *
     * @param mixed $name
     *
     * @return bool
     */
    public function supports($name)
    {
        return $this->isValidObject($name) || $name == $this->getRoute();
    }
}
