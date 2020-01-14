<?php

declare(strict_types=1);

namespace App\Request;

use function assert;
use function class_exists;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\Persistence\ObjectManager;
use function get_class;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityTransformer
{
    private PropertyAccessorInterface $propertyAccessor;

    private NamingStrategy $namingStrategy;

    private ManagerRegistry $registry;

    private RequestStack $requestStack;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        NamingStrategy $namingStrategy,
        ManagerRegistry $registry,
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

        $class = get_class($entity);
        $em = $this->registry->getManagerForClass($class);

        assert($em instanceof EntityManagerInterface);

        $name = $em->getClassMetadata($class)->getName();

        return [$this->namingStrategy->joinKeyColumnName($name) => $id];
    }

    public function reverseTransform(string $class, ?Request $request = null): ?object
    {
        if (!class_exists($class)) {
            return null;
        }

        $request ??= $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        $query = $this->namingStrategy->joinKeyColumnName($class);
        if ('' === $id = $request->query->get($query, '')) {
            return null;
        }

        $em = $this->registry->getManagerForClass($class);
        assert($em instanceof ObjectManager);

        return $em->getRepository($class)->find($id);
    }
}
