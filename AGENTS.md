# AGENTS.md

## Project: kenny1911/symfony-http-exception -> Symfony Bundle

This repo currently provides an attribute-based way to convert PHP exceptions to Symfony HttpExceptions via an `ErrorListener`. The goal: turn this into a proper Symfony Bundle (6.4/7.4/8+) that registers the listener automatically, supports attributes + config-based mappings, and keeps backward compatibility where possible.

## Goals
- Create a Symfony Bundle that auto-registers `ErrorListener` as `kernel.event_subscriber`
- Add bundle configuration (`symfony_http_exception.exceptions`) mirroring `ConfigMetadataLoader` structure 1:1
- Support **attributes + config together** via `ChainMetadataLoader`. ConfigLoader is enabled **only if `exceptions` is non-empty`. Attributes are always enabled.
- Handle optional dependencies dynamically: `symfony/translation` and `symfony/expression-language`. If missing — pass `null`. Create a **separate bundle service** for ExpressionLanguage (do not reuse a global one). Register it only if the class exists.
- Use PHP DI config: `src/Resources/config/services.php` (not YAML)
- Update README-RU.md first, then README.md
- Add minimal tests for `Configuration` (and optionally minimal for `Extension` if justified). Don't overdo bundle kernel tests.
- Keep all existing public classes/attributes/MetadataLoaders/ErrorListener/Expression API unchanged.
- Add `suggest` for translation/expression-language in `composer.json` and align Symfony constraints to `^6.4 || ^7.4 || ^8.0`

## Architecture notes
- Bundle class: `Kenny1911\SymfonyHttpException\SymfonyHttpExceptionBundle` extends `Symfony\Component\HttpKernel\Bundle\AbstractBundle`
- DI: `DependencyInjection\Configuration` + `DependencyInjection\SymfonyHttpExceptionExtension`
- Config key: `symfony_http_exception`
- `exceptions` structure (1:1 with `ConfigMetadataLoader`): keys are FQCN exception classes; values may include `statusCode`, `message`, `parameters`, `translationDomain`, `headers`
- Metadata loading: always include `AttributeMetadataLoader`; if `exceptions` non-empty include `ConfigMetadataLoader` -> combine into `ChainMetadataLoader`
- `ErrorListener` registered by bundle with tag `kernel.event_subscriber`. Arguments: `[translator_or_null, bundle_expression_language_or_null, metadata_loader]`
- Translator: use `translator` service only if `$container->has('translator')`
- ExpressionLanguage: create internal service (e.g. `kenny1911.symfony_http_exception.expression_language`) only if `Symfony\Component\ExpressionLanguage\ExpressionLanguage` exists; do not reference global `symfony.expression_language`

## Plan (track progress)
See the full detailed plan in the conversation / TODO. Key files:
- `src/SymfonyHttpExceptionBundle.php`
- `src/DependencyInjection/Configuration.php`
- `src/DependencyInjection/SymfonyHttpExceptionExtension.php`
- `src/Resources/config/services.php`
- `composer.json` (suggest + constraints)
- `README-RU.md`, `README.md`
- `tests/DependencyInjection/ConfigurationTest.php` (minimal)

## Commands (project)
- PHPUnit: `vendor/bin/phpunit` (config `phpunit.dist.xml`)
- Psalm: `vendor/bin/psalm` (`psalm.xml`)
- PHP-CS-Fixer: `vendor/bin/php-cs-fixer fix --dry-run` (`.php-cs-fixer.dist.php`)

## Notes
- Keep BC: manual registration of `ErrorListener` in user config must still be possible conceptually; bundle just adds its own service.
- ConfigLoader enabled only when `exceptions` non-empty. No `enable_attribute_loader` / `enable_config_loader` flags.
- Use PHP DI resources, not YAML.
