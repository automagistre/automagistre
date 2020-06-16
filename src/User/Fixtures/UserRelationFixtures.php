<?php

declare(strict_types=1);

namespace App\User\Fixtures;

use App\Shared\Doctrine\Registry;
use App\State;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserRelationFixtures extends Fixture
{
    private Registry $registry;

    private State $state;

    public function __construct(Registry $registry, State $state)
    {
        $this->registry = $registry;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = $this->registry->findBy(User::class, ['uuid' => AdminFixtures::ID]);
        $this->state->user($user);
    }
}
