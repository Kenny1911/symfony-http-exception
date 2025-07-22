<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Attribute;

/**
 * @api
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class HttpException extends BaseHttpException {}
