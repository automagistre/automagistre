<?php

namespace App\Request;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\NamingStrategy;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityTransformer
{
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

    public function __construct(NamingStrategy $namingStrategy, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->namingStrategy = $namingStrategy;
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function transform($entity): array
    {
        if (!method_exists($entity, 'getId')) {
            throw new \LogicException('Entity must have getId() method');
        }

        if (!$id = $entity->getId()) {
            throw new \LogicException('Entity must have id');
        }

        return [$this->namingStrategy->joinKeyColumnName(ClassUtils::getClass($entity)) => $id];
    }

    public function reverseTransform(string $class)
    {
        $request = $this->requestStack->getCurrentRequest();
        $query = $this->namingStrategy->joinKeyColumnName($class);

        if (!$id = $request->query->get($query)) {
            return null;
        }

        if (!$entity = $this->em->getRepository($class)->find($id)) {
            return null;
        }

        return $entity;
    }
}
