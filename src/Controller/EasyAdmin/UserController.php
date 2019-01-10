<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Landlord\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserController extends AbstractController
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param \stdClass $model
     */
    protected function persistEntity($model): void
    {
        $entity = new User($model->person);
        $entity->setUsername($model->username);
        $entity->setRoles($model->roles);
        $entity->changePassword($model->password, $this->encoderFactory->getEncoder($entity));

        parent::persistEntity($entity);
    }

    /**
     * @param User $entity
     */
    protected function updateEntity($entity, FormInterface $form = null): void
    {
        $password = $form->get('password')->getData();
        if (null !== $password) {
            $entity->changePassword($password, $this->encoderFactory->getEncoder($entity));
        }

        parent::updateEntity($entity);
    }
}
