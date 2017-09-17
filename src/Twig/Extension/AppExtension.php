<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Request\EntityTransformer;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var EntityTransformer
     */
    private $entityTransformer;

    public function __construct(EntityTransformer $entityTransformer)
    {
        $this->entityTransformer = $entityTransformer;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('instanceOf', [$this, 'doInstanceOf']),
            new \Twig_SimpleFunction('build', [$this, 'build']),
            new \Twig_SimpleFunction('build_time', [$this, 'buildTime']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('to_query', [$this, 'toQuery']),
        ];
    }

    public function doInstanceOf($object, $class): bool
    {
        return $object instanceof $class;
    }

    public function build(): string
    {
        return getenv('APP_BUILD');
    }

    public function buildTime(): \DateTimeImmutable
    {
        if ($time = getenv('APP_BUILD_TIME')) {
            return \DateTimeImmutable::createFromFormat(DATE_RFC3339, $time);
        }

        return new \DateTimeImmutable();
    }

    public function toQuery($entity): array
    {
        return $this->entityTransformer->transform($entity);
    }
}
