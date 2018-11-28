<?php

namespace BestIt\CtProductSlugRouter\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Exception for forbidden characters.
 *
 * @author Georgi Damyanov <georgi.damyanov@bestit-online.at>
 * @package BestIt\CtProductSlugRouter\Exception
 */
class ForbiddenCharsException extends NotFoundHttpException
{
}
