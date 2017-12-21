<?php

declare(strict_types=1);

namespace App\Request;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\NamingStrategy;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        NamingStrategy $namingStrategy,
        EntityManagerInterface $em,
        RequestStack $requestStack
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->namingStrategy = $namingStrategy;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function transform(object $entity, ?string $id = null): array
    {
        if (null === $id) {
            $id = $this->propertyAccessor->getValue($entity, 'id');
        }

        return [$this->namingStrategy->joinKeyColumnName(ClassUtils::getClass($entity)) => $id];
    }

    public function reverseTransform(string $class, ?Request $request = null): ?object
    {
        $query = $this->namingStrategy->joinKeyColumnName($class);
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (!$id = $request->query->get($query)) {
            return null;
        }

        return $this->em->getRepository($class)->find($id);
    }
}
