<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Translation;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException
 */
final class SimpleTranslator implements TranslatorInterface
{
    use TranslatorTrait;
}
