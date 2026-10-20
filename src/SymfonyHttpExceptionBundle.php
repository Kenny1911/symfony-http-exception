<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException;

use Kenny1911\SymfonyHttpException\DependencyInjection\SymfonyHttpExceptionExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @api
 */
final class SymfonyHttpExceptionBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        /** @psalm-suppress MixedReturnStatement */
        return $this->extension ??= new SymfonyHttpExceptionExtension();
    }
}
