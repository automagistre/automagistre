<?php

declare(strict_types=1);

namespace App\User\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\User\Entity\User;
use App\User\Entity\UserId;
use stdClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class UserController extends AbstractController
{
    public function __construct(private EncoderFactoryInterface $encoderFactory)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): User
    {
        $model = $entity;
        assert($model instanceof stdClass);

        $entity = new User(
            UserId::generate(),
            [],
            $model->username,
        );
        $entity->changePassword($model->password, $this->encoderFactory->getEncoder($entity));

        parent::persistEntity($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateEntity($entity, FormInterface $form = null): void
    {
        assert($entity instanceof User);

        $password = $form->get('password')->getData();

        if (null !== $password) {
            $entity->changePassword($password, $this->encoderFactory->getEncoder($entity));
        }

        parent::updateEntity($entity);
    }
}
