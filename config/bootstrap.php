<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists($path = dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    if (class_exists(Debug::class)) {
        Debug::enable();
    }
}

foreach ((array) require __DIR__.'/enums.php' as $class => [$id, $odmField]) {
    Premier\Enum\Doctrine\EnumType::register($class, $id);
    App\Shared\Enum\EnumODMType::register($class, $id, $odmField ?? 'id');
}

foreach ((array) require __DIR__.'/identifiers.php' as $class => [$id]) {
    App\Shared\Identifier\ORM\IdentifierType::register($id, $class);
    App\Shared\Identifier\ORM\IdentifierArrayType::register($id.'s', $class);
    App\Shared\Identifier\ODM\IdentifierType::register($id, $class);
}

Sentry\SentryBundle\SentryBundle::getCurrentHub()
    ->configureScope(static function (Sentry\State\Scope $scope) {
        $scope->setTag('tenant', (string) getenv('TENANT'));
    });
