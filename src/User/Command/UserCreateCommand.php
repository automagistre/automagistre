<?php

declare(strict_types=1);

namespace App\User\Command;

use App\Command\TransactionalCommand;
use App\User\Entity\User;
use App\User\Entity\UserId;
use Doctrine\ORM\EntityManagerInterface;
use MichaelPetri\TypedInput\TypedInput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserCreateCommand extends Command
{
    use TransactionalCommand;

    protected static $defaultName = 'user:create';

    public function __construct(private EncoderFactoryInterface $encoderFactory)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('user:create')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::IS_ARRAY)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function transactional(TypedInput $input, SymfonyStyle $io, EntityManagerInterface $em): int
    {
        $username = $input->getArgument('username')->asString();
        $password = $input->getArgument('password')->asString();
        $roles = $input->getArgument('roles')->asNonEmptyStrings();

        $user = new User(
            UserId::generate(),
            $roles,
            $username,
        );
        $user->changePassword($password, $this->encoderFactory->getEncoder($user));

        $em->persist($user);

        return 0;
    }
}
