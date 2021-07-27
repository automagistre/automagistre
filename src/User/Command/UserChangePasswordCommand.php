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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use function sprintf;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserChangePasswordCommand extends Command
{
    use TransactionalCommand;

    protected static $defaultName = 'user:change-password';

    public function __construct(private EncoderFactoryInterface $encoderFactory)
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('user:change-password')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function transactional(TypedInput $input, SymfonyStyle $io, EntityManagerInterface $em): int
    {
        $username = $input->getArgument('username')->asString();
        $password = $input->getArgument('password')->asString();

        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new EntityNotFoundException(sprintf('User with username "%s" not found.', $username));
        }

        $user->changePassword($password, $this->encoderFactory->getEncoder($user));

        return 0;
    }
}
