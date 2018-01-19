<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UserAddCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, EncoderFactoryInterface $passwordEncoder)
    {
        parent::__construct();

        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this->setName('user:add')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $em = $this->em;

        $user = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setPassword($input->getArgument('password'), $this->passwordEncoder->getEncoder($user));
        foreach ($input->getArgument('roles') as $role) {
            $user->addRole($role);
        }

        $em->persist($user);
        $em->flush();
    }
}
