<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Request\EntityTransformer;
use DateTimeImmutable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var EntityTransformer
     */
    private $entityTransformer;

    public function __construct(EntityTransformer $entityTransformer)
    {
        $this->entityTransformer = $entityTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('instanceOf', [$this, 'doInstanceOf']),
            new TwigFunction('build', [$this, 'build']),
            new TwigFunction('build_time', [$this, 'buildTime']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('to_query', [$this, 'toQuery']),
        ];
    }

    public function doInstanceOf($object, $class): bool
    {
        return $object instanceof $class;
    }

    public function build(): string
    {
        return getenv('APP_VERSION');
    }

    public function buildTime(): DateTimeImmutable
    {
        if ($time = getenv('APP_BUILD_TIME')) {
            return DateTimeImmutable::createFromFormat(DATE_RFC2822, $time);
        }

        return new DateTimeImmutable();
    }

    public function toQuery($entity): array
    {
        return $this->entityTransformer->transform($entity);
    }
}
