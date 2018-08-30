<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Request\EntityTransformer;
use DateTimeImmutable;
use LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(EntityTransformer $entityTransformer, ParameterBagInterface $parameterBag)
    {
        $this->entityTransformer = $entityTransformer;
        $this->parameterBag = $parameterBag;
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

    public function doInstanceOf(object $object, string $class): bool
    {
        return $object instanceof $class;
    }

    public function build(): string
    {
        return $this->parameterBag->get('app_version');
    }

    public function buildTime(): DateTimeImmutable
    {
        $string = $this->parameterBag->get('app_build_time');
        $object = DateTimeImmutable::createFromFormat(DATE_RFC2822, $string);

        if (!$object instanceof DateTimeImmutable) {
            throw new LogicException(
                \sprintf('Can\'t create "%s" from string "%s"', DateTimeImmutable::class, $string)
            );
        }

        return $object;
    }

    /**
     * @param object|array $entity
     */
    public function toQuery($entity): array
    {
        if (\is_array($entity)) {
            $queries = [];
            foreach ($entity as $item) {
                $queries[] = $this->entityTransformer->transform($item);
            }

            return \array_merge(...$queries);
        }

        return $this->entityTransformer->transform($entity);
    }
}
