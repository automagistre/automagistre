<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Closure;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EnvVarProcessor implements EnvVarProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getProvidedTypes(): array
    {
        return [
            'yaml' => 'string|array',
            'file' => 'string',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnv($prefix, $name, Closure $getEnv)
    {
        if ('file' === $prefix) {
            if (!is_scalar($file = $getEnv($name))) {
                throw new RuntimeException(sprintf('Invalid file name: env var "%s" is non-scalar.', $name));
            }

            if (!file_exists($file)) {
                return null;
            }

            $content = file_get_contents($file);
            if (is_string($content)) {
                $content = trim($content);
            }

            return $content;
        }

        if ('yaml' === $prefix) {
            return Yaml::parse($getEnv($name), Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_CONSTANT);
        }

        throw new RuntimeException(sprintf('Unsupported env var prefix "%s".', $prefix));
    }
}
