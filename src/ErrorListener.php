<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;
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
        private readonly ?TranslatorInterface $translator = null,
        private readonly ?ExpressionLanguage $expressionLanguage = null,
    ) {}

    #[\Override]
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
            $message = $httpException->message ?? $throwable->getMessage();

            if (null !== $this->translator) {
                if (null !== $this->expressionLanguage) {
                    $parameters = array_map(
                        fn(string $expr): string => (string) $this->expressionLanguage?->evaluate(
                            expression: $expr,
                            values: [
                                'e' => $throwable,
                                'translator' => null !== $this->translator
                                    ? new TranslatorDecorator($this->translator, $httpException->translationDomain)
                                    : null,
                            ],
                        ),
                        $httpException->translationParameters,
                    );
                } else {
                    $parameters = [];
                }

                $message = $this->translator->trans($message, $parameters, $httpException->translationDomain);
            }

            $event->setThrowable(new HttpException(
                statusCode: $httpException->statusCode,
                message: $message,
                previous: $throwable,
                headers: $httpException->headers,
                code: (int) $throwable->getCode(),
            ));

            break;
        } while ($refClass = $refClass->getParentClass());
    }
}
