<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Log\LogManager;
use Illuminate\Support\Env;
use Orchestra\Testbench\Exceptions\DeprecatedException;
use function Orchestra\Testbench\phpunit_version_compare;

/**
 * @internal
 */
final class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    /**
     * Testbench Class.
     *
     * @var \PHPUnit\Framework\TestCase|null
     */
    protected $testbench;

    /**
     * Create a new exception handler instance.
     *
     * @param  \PHPUnit\Framework\TestCase|null  $testbench
     */
    public function __construct($testbench = null)
    {
        $this->testbench = $testbench;
    }

    /**
     * Reports a deprecation to the "deprecations" logger.
     *
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  int  $level
     * @return void
     *
     * @throws \Orchestra\Testbench\Exceptions\DeprecatedException
     */
    public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
    {
        parent::handleDeprecationError($message, $file, $line, $level);

        $testbenchConvertDeprecationsToExceptions = Env::get('TESTBENCH_CONVERT_DEPRECATIONS_TO_EXCEPTIONS');

        $error = new DeprecatedException($message, $level, $file, $line);

        if ($testbenchConvertDeprecationsToExceptions === true) {
            throw $error;
        }

        if ($testbenchConvertDeprecationsToExceptions !== false && $this->getPhpUnitConvertDeprecationsToExceptions() === true) {
            throw $error;
        }
    }

    /**
     * Ensure the "deprecations" logger is configured.
     *
     * @return void
     */
    protected function ensureDeprecationLoggerIsConfigured()
    {
        /** @phpstan-ignore-next-line */
        with(self::$app['config'], function ($config) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            if ($config->get('logging.channels.deprecations')) {
                return;
            }

            /** @var array{channel?: string, trace?: bool}|string|null $options */
            $options = $config->get('logging.deprecations');

            if (\is_array($options)) {
                $driver = $options['channel'] ?? 'null';
            } else {
                $driver = $options ?? 'null';
            }

            if ($driver === 'single') {
                $config->set('logging.channels.deprecations', array_merge($config->get('logging.channels.single'), [
                    'path' => static::$app->storagePath('logs/deprecations.log'),
                ]));
            } else {
                $config->set('logging.channels.deprecations', $config->get("logging.channels.{$driver}"));
            }

            $config->set('logging.deprecations', [
                'channel' => 'deprecations',
                'trace' => true,
            ]);
        });
    }

    /**
     * Get PHPUnit convert deprecations to exceptions from TestResult.
     *
     * @phpunit-overrides
     *
     * @return bool
     */
    protected function getPhpUnitConvertDeprecationsToExceptions(): bool
    {
        if (phpunit_version_compare('10', '>=')) {
            return false;
        }

        /** @var \PHPUnit\Framework\TestResult|null $testResult */
        $testResult = $this->testbench?->getTestResultObject();

        return $testResult?->getConvertDeprecationsToExceptions() ?? false;
    }

    /**
     * Determine if deprecation error should be ignored.
     *
     * @return bool
     */
    protected function shouldIgnoreDeprecationErrors()
    {
        return ! class_exists(LogManager::class)
            || ! self::$app->hasBeenBootstrapped();
    }
}
