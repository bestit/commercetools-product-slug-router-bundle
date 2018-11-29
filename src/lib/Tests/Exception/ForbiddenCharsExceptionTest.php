<?php

declare(strict_types=1);

namespace BestIt\CtProductSlugRouter\Tests\Exception;

use BestIt\CtProductSlugRouter\Exception\ForbiddenCharsException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ForbiddenCharsExceptionTest
 *
 * @author Georgi Damyanov <georgi.damyanov@bestit-online.at>
 * @package BestIt\CtProductSlugRouter\Tests\Exception
 */
class ForbiddenCharsExceptionTest extends TestCase
{

    /**
     * Tests that the forbidden chars exception is not found
     *
     * @return void
     */
    public function testType()
    {
        static::assertInstanceOf(NotFoundHttpException::class, new ForbiddenCharsException());
    }
}
