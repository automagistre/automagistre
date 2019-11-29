<?php

declare(strict_types=1);

namespace App\Form\TypeGuesser;

use App\Form\Model\Model;
use function assert;
use function class_exists;
use function is_subclass_of;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\FormTypeGuesserInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityModelTypeGuesser implements FormTypeGuesserInterface
{
    private DoctrineOrmTypeGuesser $guesser;

    public function __construct(DoctrineOrmTypeGuesser $guesser)
    {
        $this->guesser = $guesser;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function guessRequired($class, $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function guessMaxLength($class, $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function guessPattern($class, $property)
    {
        return $this->guess(__FUNCTION__, $class, $property);
    }

    /**
     * @return mixed
     */
    private function guess(string $method, string $class, string $property)
    {
        if (!is_subclass_of(Model::class, $class)) {
            return;
        }

        assert(class_exists($class));

        /* @var Model $class */
        return $this->guesser->{$method}($class::getEntityClass(), $property);
    }
}
