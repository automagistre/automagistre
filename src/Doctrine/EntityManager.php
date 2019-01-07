<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait EntityManager
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    protected function getManager(): EntityManagerInterface
    {
        $em = $this->registry->getManager('default');

        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManager required.');
        }

        return $em;
    }
}
