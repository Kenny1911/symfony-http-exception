<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\Attribute\BadRequestHttpException;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests
 */
#[BadRequestHttpException]
final class TestBadRequestHttpException extends \Exception {}
