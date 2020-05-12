<?php

declare(strict_types=1);

namespace App\User\Fixtures;

use App\Doctrine\Registry;
use App\State;
use App\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class UserRelationFixtures extends Fixture implements FixtureGroupInterface
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

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }
}
