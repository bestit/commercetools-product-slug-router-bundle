<?php

namespace BestIt\CtProductSlugRouter\Tests;

use BestIt\CtProductSlugRouter\Exception\ProductNotFoundException;
use BestIt\CtProductSlugRouter\Repository\ProductRepositoryInterface;
use BestIt\CtProductSlugRouter\Router\ProductRouter;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Testing of the product router.
 * @author despendiller <espendiller@bestit-online.de>
 * @category Tests
 * @package BestIt\CtProductSlugRouter
 * @subpackage Router
 * @version $id$
 */
class ProductRouterTest extends TestCase
{
    /**
     * Tests if product routing support is false.
     * @covers \BestIt\CtProductSlugRouter\Router\ProductRouter::supports
     */
    public function testThatSupportsIsFalseAlways()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $router = new ProductRouter($er);
        static::assertFalse($router->supports(new DateTime()));
    }

    /**
     * Tests if product routers generate function is not supported
     *
     * @covers \BestIt\CtProductSlugRouter\Router\ProductRouter::generate
     * @expectedException \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testThatGeneratesIsNotSupported()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $router = new ProductRouter($er);
        $router->generate('foobar');
    }

    /**
     * Tests if a product with a slug is providing an url
     *
     * @covers \BestIt\CtProductSlugRouter\Router\ProductRouter::match
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testProductWithSlugShouldProvideUrl()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $product = new Product();
        $product->setSlug('/foo');

        $er->method('getProductBySlug')
            ->willReturn($product);

        $router = new ProductRouter($er);
        $result = $router->match('foobar');

        static::assertInstanceOf(
            'BestIt\Frontend\ProductBundle\Model\Product',
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
     * Tests if product not found is a resource and not an exception
     *
     * @covers \BestIt\CtProductSlugRouter\Router\ProductRouter::match
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
     */
    public function testProductNotFoundShouldBeResourceNotFoundException()
    {
        $er = $this->createMock(ProductRepositoryInterface::class);

        $product = new Product();
        $product->setSlug('/foo');

        $er->method('getProductBySlug')
            ->willThrowException(new ProductNotFoundException());

        $router = new ProductRouter($er);
        $router->match('foobar');
    }
}
