<?php

declare(strict_types=1);

namespace App\Request;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityTransformer
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var NamingStrategy
     */
    private $namingStrategy;

    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        NamingStrategy $namingStrategy,
        RegistryInterface $registry,
        RequestStack $requestStack
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->namingStrategy = $namingStrategy;
        $this->registry = $registry;
        $this->requestStack = $requestStack;
    }

    public function transform(object $entity, ?string $id = null): array
    {
        if (null === $id) {
            $id = $this->propertyAccessor->getValue($entity, 'id');
        }

        $class = \get_class($entity);
        $em = $this->registry->getManagerForClass($class);
        $name = $em->getClassMetadata($class)->getName();

        return [$this->namingStrategy->joinKeyColumnName($name) => $id];
    }

    public function reverseTransform(string $class, ?Request $request = null): ?object
    {
        if (!\class_exists($class)) {
            return null;
        }

        $query = $this->namingStrategy->joinKeyColumnName($class);
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if ('' === $id = $request->query->get($query, '')) {
            return null;
        }

        $em = $this->registry->getManagerForClass($class);
        \assert($em instanceof ObjectManager);

        return $em->getRepository($class)->find($id);
    }
}
