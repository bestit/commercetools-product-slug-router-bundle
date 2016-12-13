<?php

namespace BestIt\Frontend\ProductBundle\Tests\Router;

use BestIt\Frontend\ProductBundle\Exception\ProductNotFoundException;
use BestIt\Frontend\ProductBundle\Model\Product;
use BestIt\Frontend\ProductBundle\Router\ProductRouter;

/**
 * Class ProductRouterTest
 *
 * @package BestIt\Frontend\ProductBundle\Tests\Router
 */
class ProductRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if product routing support is false
     *
     * @covers \BestIt\Frontend\ProductBundle\Router\ProductRouter::supports
     */
    public function testThatSupportsIsFalseAlways()
    {
        $er = $this->getMockBuilder('BestIt\Frontend\ProductBundle\Repository\ProductRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $router = new ProductRouter($er);
        static::assertFalse($router->supports(new \DateTime()));
    }

    /**
     * Tests if product routers generate function is not supported
     *
     * @covers \BestIt\Frontend\ProductBundle\Router\ProductRouter::generate
     * @expectedException \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testThatGeneratesIsNotSupported()
    {
        $er = $this->getMockBuilder('BestIt\Frontend\ProductBundle\Repository\ProductRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $router = new ProductRouter($er);
        $router->generate('foobar');
    }

    /**
     * Tests if a product with a slug is providing an url
     *
     * @covers \BestIt\Frontend\ProductBundle\Router\ProductRouter::match
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testProductWithSlugShouldProvideUrl()
    {
        $er = $this->getMockBuilder('BestIt\Frontend\ProductBundle\Repository\ProductRepository')
            ->disableOriginalConstructor()
            ->getMock();

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
     * @covers \BestIt\Frontend\ProductBundle\Router\ProductRouter::match
     * @expectedException \Symfony\Component\Routing\Exception\ResourceNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MethodNotAllowedException
     */
    public function testProductNotFoundShouldBeResourceNotFoundException()
    {
        $er = $this->getMockBuilder('BestIt\Frontend\ProductBundle\Repository\ProductRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $product = new Product();
        $product->setSlug('/foo');

        $er->method('getProductBySlug')
            ->willThrowException(new ProductNotFoundException());

        $router = new ProductRouter($er);
        $router->match('foobar');
    }
}
