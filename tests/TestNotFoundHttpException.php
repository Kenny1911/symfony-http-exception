<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\Attribute\NotFoundHttpException;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests
 */
#[NotFoundHttpException]
final class TestNotFoundHttpException extends \Exception {}
