<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\Attribute\AccessDeniedHttpException;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests
 */
#[AccessDeniedHttpException]
final class TestAccessDeniedHttpException extends \Exception {}
