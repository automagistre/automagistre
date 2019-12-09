<?php

declare(strict_types=1);

namespace App\User\Command;

use App\Doctrine\Registry;
use App\User\Entity\User;
use function array_flip;
use function array_key_exists;
use Doctrine\ORM\EntityNotFoundException;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserDemoteCommand extends Command
{
    protected static $defaultName = 'user:demote';

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        parent::__construct();

        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('user:demote')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::IS_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->registry->manager(User::class);

        ['username' => $username, 'roles' => $roles] = $input->getArguments();

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

        $em->flush();

        return 0;
    }
}
