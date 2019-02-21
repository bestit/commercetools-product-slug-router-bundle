<?php

namespace BestIt\CtProductSlugRouter\Tests;

use BestIt\CtProductSlugRouter\Exception\ForbiddenCharsException;
use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouter\Repository\ProductRepositoryInterface;
use BestIt\CtProductSlugRouter\Router\ProductRouter;
use Commercetools\Core\Model\Common\Context;
use Commercetools\Core\Model\Common\LocalizedString;
use Commercetools\Core\Model\Product\ProductProjection;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use function json_encode;
use function uniqid;

/**
 * Testing of the product router.
 *
 * @author despendiller <espendiller@bestit-online.de>
 * @package BestIt\CtProductSlugRouter\Tests
 */
class ProductRouterTest extends TestCase
{
    /**
     * The tested router.
     *
     * @var ProductRouter|null
     */
    private $fixture;

    /**
     * The injected repository.
     *
     * @var PHPUnit_Framework_MockObject_MockObject|ProductRepositoryInterface|null
     */
    private $repository;

    /**
     * Returns the fixture
     *
     * @param string $filename
     *
     * @return string
     */
    private function getFixture(string $filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Returns a decoded product fixture
     *
     * @param string $filename
     *
     * @return ProductProjection
     */
    private function getProductFixture(string $filename)
    {
        return ProductProjection::fromArray(
            json_decode(file_get_contents($this->getFixture($filename)), true),
            Context::of()->setLocale('de')->setLanguages(['de'])
        );
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new ProductRouter(
            $this->repository = $this->createMock(ProductRepositoryInterface::class)
        );
    }

    /**
     * Test that generate method throws exception if slug param is missing
     *
     * @return void
     */
    public function testGenerateByRouteNameWithoutSlugParam()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->fixture->generate('best_it_frontend_product_detail_index');
    }

    /**
     * Test that generate method creates route
     *
     * @return void
     */
    public function testGenerateRouteByName()
    {
        $this->fixture->setContext(new RequestContext());

        static::assertSame(
            '/foobar',
            $this->fixture->generate('best_it_frontend_product_detail_index', ['slug' => 'foobar'])
        );
    }

    /**
     * Test that generate method creates route with query
     *
     * @return void
     */
    public function testGenerateRouteByNameWithQuery()
    {
        $this->fixture->setContext(new RequestContext());

        static::assertSame(
            '/foobar?best=it',
            $this->fixture->generate('best_it_frontend_product_detail_index', ['slug' => 'foobar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route with query
     *
     * @return void
     */
    public function testGenerateRouteByNameWithQueryAndBaseUrl()
    {
        $this->fixture->setContext(new RequestContext('/app_dev.php'));

        static::assertSame(
            '/app_dev.php/foobar?best=it',
            $this->fixture->generate('best_it_frontend_product_detail_index', ['slug' => 'foobar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObject()
    {
        $this->fixture->setContext(new RequestContext());

        $category = $this->getProductFixture('product.json');

        static::assertSame(
            '/073554_04000119_24er_bl_alkaline',
            $this->fixture->generate($category)
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObjectWithQuery()
    {
        $this->fixture->setContext(new RequestContext());

        $category = $this->getProductFixture('product.json');

        static::assertSame(
            '/073554_04000119_24er_bl_alkaline?foo=bar&best=it',
            $this->fixture->generate($category, ['foo' => 'bar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method creates route by object
     *
     * @return void
     */
    public function testGenerateRouteByObjectWithQueryAndBaseUrl()
    {
        $this->fixture->setContext(new RequestContext('/app_dev.php'));

        $category = $this->getProductFixture('product.json');

        static::assertSame(
            '/app_dev.php/073554_04000119_24er_bl_alkaline?foo=bar&best=it',
            $this->fixture->generate($category, ['foo' => 'bar', 'best' => 'it'])
        );
    }

    /**
     * Test that generate method throws exception when absolute url is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForAbsoluteUrl()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->fixture->generate('foobar', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Test that generate method throws exception when network path is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForNetworkPath()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->fixture->generate('foobar', [], UrlGeneratorInterface::NETWORK_PATH);
    }

    /**
     * Test that generate method throws exception when relative path type is used
     *
     * @return void
     */
    public function testGenerateThrowsExceptionForRelativePath()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->fixture->generate('foobar', [], UrlGeneratorInterface::RELATIVE_PATH);
    }

    /**
     * Test that generate method throws exception
     *
     * @return void
     */
    public function testGenerateThrowsNotFoundException()
    {
        $this->expectException(RouteNotFoundException::class);

        $this->fixture->generate('foobar');
    }

    /**
     * Checks if the id is returned, if an object is given.
     *
     * @return void
     */
    public function testGetRouteDebugMessageWithObject()
    {
        $product = ProductProjection::fromArray(['id' => $id = uniqid()]);

        static::assertSame(
            sprintf(
                '%s:%s(%s)',
                get_class($this->fixture),
                $id,
                json_encode($params = ['foo' => 'bar'])
            ),
            $this->fixture->getRouteDebugMessage($product, $params)
        );
    }

    /**
     * Checks if the string is returned, if a string is given.
     *
     * @return void
     */
    public function testGetRouteDebugMessageWithString()
    {
        static::assertSame(
            sprintf(
                '%s:%s(%s)',
                get_class($this->fixture),
                $name = uniqid(),
                json_encode($params = ['foo' => 'bar'])
            ),
            $this->fixture->getRouteDebugMessage($name, $params)
        );
    }

    /**
     * Check if the parameter contains special characters
     *
     * @return void
     */
    public function testMatchFailedWithForbiddenCharsException()
    {
        $this->fixture->setContext(new RequestContext());

        $this->expectException(ForbiddenCharsException::class);

        $this->fixture->match('!"§');
    }

    /**
     * Tests if product not found is a resource and not an exception
     *
     * @covers \BestIt\CtProductSlugRouter\Router\ProductRouter::match
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     * @throws MethodNotAllowedException
     *
     * @return void
     */
    public function testProductNotFoundShouldBeResourceNotFoundException()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $er->method('getProductBySlug')
            ->willThrowException(new ProductNotFoundException());

        $this->fixture = new ProductRouter($er);
        $this->fixture->match('foobar');
    }

    /**
     * Tests if a product with a slug is providing an url
     *
     * @covers \BestIt\CtProductSlugRouter\Router\ProductRouter::match
     *
     * @throws MethodNotAllowedException
     * @throws ResourceNotFoundException
     *
     * @return void
     */
    public function testProductWithSlugShouldProvideUrl()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $product = new ProductProjection();
        $product->setSlug(LocalizedString::ofLangAndText('de', '/foo'));

        $er->method('getProductBySlug')
            ->willReturn($product);

        $this->fixture = new ProductRouter($er);
        $result = $this->fixture->match('foobar');

        static::assertInstanceOf(
            ProductProjection::class,
            $result['product']
        );

        static::assertEquals(
            'BestIt\Frontend\ProductBundle\Controller\DetailController::indexAction',
            $result['_controller']
        );

        static::assertEquals(
            'best_it_frontend_product_detail_index',
            $result['_route']
        );
    }

    /**
     * Tests if product routing support is false.
     *
     * @return void
     */
    public function testThatSupportsIsFalseAlways()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $this->fixture = new ProductRouter($er);
        static::assertFalse($this->fixture->supports(new DateTime()));
    }
}
