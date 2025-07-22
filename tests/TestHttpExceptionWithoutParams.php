<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests
 */
#[HttpException(statusCode: Response::HTTP_BAD_REQUEST)]
final class TestHttpExceptionWithoutParams extends \Exception {}
