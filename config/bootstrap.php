<?php

use Symfony\Component\Dotenv\Dotenv;

require \dirname(__DIR__).'/vendor/autoload.php';

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (\is_array($env = @include \dirname(__DIR__).'/.env.local')) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== \strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
} elseif (!\class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
} else {
    // load all the .env files
    (new Dotenv(false))->loadEnv(\dirname(__DIR__).'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || \filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

Premier\Enum\Doctrine\EnumType::register(App\Enum\Carcase::class, 'carcase_enum');
Premier\Enum\Doctrine\EnumType::register(App\Enum\CarTransmission::class, 'car_transmission_enum');
Premier\Enum\Doctrine\EnumType::register(App\Enum\CarWheelDrive::class, 'car_wheel_drive_enum');
Premier\Enum\Doctrine\EnumType::register(App\Enum\EngineType::class, 'engine_type_enum');
Premier\Enum\Doctrine\EnumType::register(App\Enum\NoteType::class, 'note_type_enum');
Premier\Enum\Doctrine\EnumType::register(App\Enum\OrderStatus::class, 'order_status_enum');
Premier\Enum\Doctrine\EnumType::register(App\Enum\Tenant::class, 'tenant_enum');
