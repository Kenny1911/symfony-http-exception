<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;
use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;
use Kenny1911\SymfonyHttpException\Translation\SimpleTranslator;
use Kenny1911\SymfonyHttpException\Translation\TranslatorDecorator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @api
 */
final class ErrorListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator = new SimpleTranslator(),
        private readonly ?ExpressionLanguage $expressionLanguage = null,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 128],
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof HttpException) {
            return;
        }

        $refClass = new \ReflectionClass($throwable::class);

        do {
            $attribute = $refClass->getAttributes(BaseHttpException::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;

            if (null === $attribute) {
                continue;
            }

            /** @var BaseHttpException $httpException */
            $httpException = $attribute->newInstance();

            $translator = new TranslatorDecorator($this->translator, $httpException->translationDomain);

            $parameters = array_map(fn(string|Expression $p): string => $this->exec($p, $throwable, $translator), $httpException->parameters);
            $message = $translator->trans($httpException->message ?? $throwable->getMessage(), $parameters, $httpException->translationDomain);
            $headers = array_map(fn(string|Expression $p): string => $this->exec($p, $throwable, $translator), $httpException->headers);

            $event->setThrowable(new HttpException(
                statusCode: $httpException->statusCode,
                message: $message,
                previous: $throwable,
                headers: $headers,
                code: (int) $throwable->getCode(),
            ));

            break;
        } while ($refClass = $refClass->getParentClass());
    }

    private function exec(string|Expression $expression, \Throwable $throwable, TranslatorInterface $translator): string
    {
        if ($expression instanceof Expression && null !== $this->expressionLanguage) {
            return (string) $this->expressionLanguage->evaluate(
                expression: (string) $expression,
                values: [
                    'e' => $throwable,
                    'translator' => $translator,
                ],
            );
        }

        return (string) $expression;
    }
}
