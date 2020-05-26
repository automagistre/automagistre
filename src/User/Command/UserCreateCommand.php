<?php

declare(strict_types=1);

namespace App\User\Command;

use App\Shared\Doctrine\Registry;
use App\User\Entity\User;
use App\User\Entity\UserId;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserCreateCommand extends Command
{
    protected static $defaultName = 'user:create';

    private Registry $registry;

    private EncoderFactoryInterface $encoderFactory;

    public function __construct(Registry $registry, EncoderFactoryInterface $encoderFactory)
    {
        parent::__construct();

        $this->registry = $registry;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('user:create')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::IS_ARRAY);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->registry->manager(User::class);

        ['username' => $username, 'password' => $password] = $input->getArguments();

        $user = new User(
            UserId::generate(),
            (array) $input->getArgument('roles'),
            $username,
            null
        );
        $user->changePassword($password, $this->encoderFactory->getEncoder($user));

        $em->persist($user);
        $em->flush();

        return 0;
    }
}
