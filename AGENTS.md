# AGENTS.md

## Project
`kenny1911/symfony-http-exception` is a Symfony Bundle (`"type": "symfony-bundle"`) that converts PHP exceptions into `Symfony\Component\HttpKernel\Exception\HttpException` via attributes and config.
- PHP `^8.1`; Symfony `^6.4 || ^7.4 || ^8.0`.
- `symfony/translation` and `symfony/expression-language` are optional (`suggest`), never hard requirements.
- Public API (`ErrorListener`, attributes, `MetadataLoader`s, `Expression`) is marked `@api` — do not break BC.

## Architecture
- Bundle: `src/SymfonyHttpExceptionBundle.php` extends `Symfony\Component\HttpKernel\Bundle\Bundle`; overrides `getContainerExtension()` to return `SymfonyHttpExceptionExtension`.
- DI: `src/DependencyInjection/Configuration.php` + `SymfonyHttpExceptionExtension.php` (both `@internal`, `@psalm-internal Kenny1911\SymfonyHttpException`).
- **Extension alias = config root key = `kenny1911_symfony_http_exception`** (not `symfony_http_exception`).
- Container parameters: `kenny1911.symfony_http_exception.enable_attributes`, `kenny1911.symfony_http_exception.exceptions`.
- Config: `enable_attributes` (bool, default `true`); `exceptions` — key = exception FQCN, values: `statusCode` (required, 400–599), `message?`, `parameters?`, `translationDomain?` (default `http_message`), `headers?`.
- DI resources: `config/services.php` **at package root** (not `src/Resources/config`). File namespace is `Kenny1911\SymfonyHttpException`; import DSL functions via `use function Symfony\Component\DependencyInjection\Loader\Configurator\...`.
- `config/services.php` reads params through its second argument `ContainerBuilder $builder`; the Extension must set parameters **before** `$loader->load('services.php')`.
- Loaders: `AttributeMetadataLoader` (if `enable_attributes`), `ConfigMetadataLoader` (if `exceptions` non-empty) → `ChainMetadataLoader` (always).
- `ErrorListener` is tagged `kernel.event_subscriber` with args `[translator, expressionLanguage|null, metadataLoader]`.
- translator: `service('translator')` if present, otherwise `inline_service(SimpleTranslator::class)` — **not `null`** (the argument is non-nullable).
- ExpressionLanguage: dedicated service `kenny1911.symfony_http_exception.expression_language`, registered only if `class_exists()`; never reuse global `symfony.expression_language`.

## Commands
- Install: `composer install` (`vendor/` and `composer.lock` are gitignored).
- Tests: `vendor/bin/phpunit` (`phpunit.dist.xml`). Single test: `vendor/bin/phpunit --filter testName tests/DependencyInjection/ConfigurationTest.php`.
- All checks: `composer run checks` (cs-check → phpunit → psalm --no-cache → composer-require-checker → composer-unused).
- Psalm: `./tools/psalm/vendor/bin/psalm` (or `composer run psalm`).
- CS-Fixer: `./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix` (`cs-fix`) / `check` (`cs-check`).
- Symfony version matrix: `bin/check-multiply-versions.sh` (`^6.4`, `^7.4`, `8.0.*`, `8.1.*`; translation-contracts `^2.3`/`^3.0`).
- Tooling lives in `./tools/<name>/` with its own `composer.json` — this is **not** the root `vendor/bin`.

## Tests
- `tests/`, PSR-4 `Kenny1911\SymfonyHttpException\Tests\`; test classes are marked `@internal`.
- Assert the **whole resulting array with a single `assertSame`**, not a pile of `self::assert*`.
- Extract `processConfiguration` into a private helper (see `ConfigurationTest::process()`).
- For config, use the full array or `@psalm-import-type Config from Configuration`.
- Reference: `tests/ErrorListenerTest.php`, `tests/DependencyInjection/ConfigurationTest.php`.

## Gotchas
- Psalm: `errorLevel="1"`, analyses only `src` and `tests` — **`config/` is not covered**; check `config/services.php` manually (`psalm config/services.php`).
- Psalm forbids: `dd`, `die`, `dump`, `echo`, `empty`, `eval`, `exit`, `print`, `print_r`, `var_export`.
- CS-Fixer: `PHPyh\CodingStandard\PhpCsFixerCodingStandard`, `yoda_style=true`, `ClassVisibilityFixer` (default `api`); Finder covers only `src` and `tests`, so **`config/` is not linted**.
- `->cannotBeEmpty()` is invalid on `integerNode` (`NumericNodeDefinition`) — use `min()`/`max()`/`isRequired()`.
- `@psalm-suppress` exists only for the Symfony Config DSL (`Configuration`) and `MixedReturnStatement` in the Bundle; prefer `assert()` for new code.
- CI (`.github/workflows/checks.yml`): PHP 8.4, installs tools in `./tools/`, then runs `bin/check-multiply-versions.sh`.
- `config/` is runtime DI config — keep it out of `.gitattributes export-ignore`.

## Remaining work
- Update `README-RU.md`, then `README.md`: register the bundle instead of manual `services.yaml`.
- Optional: `tests/DependencyInjection/ExtensionTest.php`.
- Optional: add `config/` to `psalm.xml` and `.php-cs-fixer.dist.php`.
