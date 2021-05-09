<?php

declare(strict_types=1);

namespace App\Form\TypeGuesser;

use App\Form\Model\Model;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\FormTypeGuesserInterface;
use function is_subclass_of;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityModelTypeGuesser implements FormTypeGuesserInterface
{
    public function __construct(private DoctrineOrmTypeGuesser $guesser)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function guessType(string $class, string $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function guessRequired(string $class, string $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function guessMaxLength(string $class, string $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function guessPattern(string $class, string $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * @return mixed
     */
    private function guess(string $method, string $class, string $property)
    {
        /** @phpstan-ignore-next-line */
        if (!is_subclass_of(Model::class, $class)) {
            return null;
        }

        /** @var callable $callable */
        $callable = $class.'::getEntityClass';

        /** @phpstan-ignore-next-line */
        return $this->guesser->{$method}($callable(), $property);
    }
}
