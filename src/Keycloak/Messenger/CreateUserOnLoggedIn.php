<?php

declare(strict_types=1);

namespace App\Keycloak\Messenger;

use App\Customer\Entity\Person;
use App\Doctrine\Registry;
use App\Keycloak\Event\UserLoggedIn;
use App\MessageBus\MessageHandler;
use App\User\Entity\User;
use Keycloak\Admin\KeycloakClient;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use function Sentry\captureMessage;
use function sprintf;

final class CreateUserOnLoggedIn implements MessageHandler
{
    public function __construct(
        private Registry $registry,
        private KeycloakClient $keycloak,
    ) {
    }

    public function __invoke(UserLoggedIn $event): void
    {
        foreach ($this->keycloak->getUsers() as $user) {
            if ($user['username'] === $event->username) {
                return;
            }
        }

        $user = $this->registry->findOneBy(User::class, ['username' => $event->username]);

        if (null === $user) {
            captureMessage(sprintf('No user found for %s username.', $event->username));

            return;
        }

        $person = $this->registry->findOneBy(Person::class, ['email' => $user->getUsername()]);

        $telephone = $person?->telephone;

        if (null !== $telephone) {
            $telephone = PhoneNumberUtil::getInstance()->format($telephone, PhoneNumberFormat::E164);
        }

        $this->keycloak->createUser([
            'attributes' => [
                'user_id' => $user->toId()->toString(),
                'customer_id' => $person?->id->toString(),
                'phone' => $telephone,
            ],
            'username' => $user->getUsername(),
            'firstName' => $user->firstName ?? $person?->firstname,
            'lastName' => $user->lastName ?? $person?->lastname,
            'email' => $user->getUsername(),
            'emailVerified' => true,
            'enabled' => true,
            'credentials' => [
                [
                    'type' => 'password',
                    'value' => $event->password,
                ],
            ],
            'groups' => [
                'operator',
            ],
        ]);
    }
}
