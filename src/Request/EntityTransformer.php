<?php

declare(strict_types=1);

namespace App\Request;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\NamingStrategy;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @param $entity
     *
     * @throws \LogicException
     *
     * @return array
     */
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

    /**
     * @param string       $class
     * @param Request|null $request
     *
     * @return object|null
     */
    public function reverseTransform(string $class, Request $request = null)
    {
        $query = $this->namingStrategy->joinKeyColumnName($class);
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (!$id = $request->query->get($query)) {
            return null;
        }

        if (!$entity = $this->em->getRepository($class)->find($id)) {
            return null;
        }

        return $entity;
    }
}
