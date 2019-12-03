<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Doctrine\Registry;
use App\User\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserChangePasswordCommand extends Command
{
    protected static $defaultName = 'user:change-password';

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
        $this
            ->setName('user:change-password')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->registry->manager(User::class);

        ['username' => $username, 'password' => $password] = $input->getArguments();

        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new EntityNotFoundException(sprintf('User with username "%s" not found.', $username));
        }

        $user->changePassword($password, $this->encoderFactory->getEncoder($user));

        $em->flush();

        return 0;
    }
}
