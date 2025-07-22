<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException
 */
final class TranslatorDecorator implements TranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ?string $defaultDomain,
    ) {}

    #[\Override]
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain ?? $this->defaultDomain, $locale);
    }

    #[\Override]
    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }
}
