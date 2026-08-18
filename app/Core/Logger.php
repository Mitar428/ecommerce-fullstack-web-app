<?php

namespace Ecommerce\Shop\Core;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;


class Logger
{
    private static ?MonologLogger $instance = null;

    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = new MonologLogger('ecommerce');

            self::$instance->pushHandler(
                new StreamHandler(__DIR__ . '/../../logs/app.log', MonologLogger::DEBUG)
            );
        }

        return self::$instance;
    }

    public static function info(string $message, array $context = []): void
    {
        self::getInstance()->info($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::getInstance()->warning($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->error($message, $context);
    }

    public static function logAction(string $action, array $details = []): void
    {
        self::info("Akcija: {$action}", [
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}