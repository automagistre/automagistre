<?php

declare(strict_types=1);

namespace App\User\Command;

use App\Command\TransactionalCommand;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use MichaelPetri\TypedInput\TypedInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_flip;
use function array_key_exists;
use function sprintf;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserDemoteCommand extends Command
{
    use TransactionalCommand;

    protected static $defaultName = 'user:demote';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('user:demote')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::IS_ARRAY)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function transactional(TypedInput $input, SymfonyStyle $io, EntityManagerInterface $em): int
    {
        $username = $input->getArgument('username')->asString();
        $roles = $input->getArgument('roles')->asNonEmptyStrings();

        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new EntityNotFoundException(sprintf('User with username "%s" not found.', $username));
        }

        $currentRoles = array_flip($user->getRoles());
        foreach ($roles as $role) {
            if (array_key_exists($role, $currentRoles)) {
                unset($currentRoles[$role]);
            }
        }

        $user->setRoles($currentRoles);

        return 0;
    }
}
