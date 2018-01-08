<?php

declare(strict_types=1);

namespace App\Form\TypeGuesser;

use App\Form\Model\Model;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\FormTypeGuesserInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EntityModelTypeGuesser implements FormTypeGuesserInterface
{
    /**
     * @var DoctrineOrmTypeGuesser
     */
    private $guesser;

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

    private function guess(string $method, string $class, string $property)
    {
        if (!is_subclass_of(Model::class, $class)) {
            return;
        }

        /** @var Model $class */
        return $this->guesser->{$method}($class::getEntityClass(), $property);
    }
}
