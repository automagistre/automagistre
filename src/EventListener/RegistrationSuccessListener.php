<?php

namespace App\EventListener;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RegistrationSuccessListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManager $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onUserRegistration',
        ];
    }

    public function onUserRegistration(FormEvent $event)
    {
        $form = $event->getForm();

        /** @var User $user */
        $user = $form->getData();

        $person = $this->em->getRepository(Person::class)->findOneBy(['email' => $user->getEmail()]);
        if (!$person) {
            $person = new Person();
            $person->setFirstname($form->get('firstname')->getData());
            $person->setLastname($form->get('lastname')->getData());
            $person->setEmail($user->getEmail());
        }

        $user->setPerson($person);

        $event->setResponse(new RedirectResponse($this->router->generate('easyadmin')));
    }
}
