<?php

declare(strict_types=1);

namespace App\RoadRunner\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SessionListener implements EventSubscriberInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var array
     */
    private $sessionStorageOptions;

    public function __construct(SessionInterface $session, array $sessionStorageOptions)
    {
        $this->session = $session;
        $this->sessionStorageOptions = $sessionStorageOptions;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', -9998],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $session = $this->session;
        $request = $event->getRequest();

        if ($session->isStarted()) {
            $this->closeSession();
        }

        $session->setId($request->cookies->get($session->getName(), ''));
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $session = $this->session;
        if ($session->isStarted()) {
            $this->closeSession();
        }

        $request = $event->getRequest();

        if (\in_array($session->getId(), ['', $request->cookies->get($session->getName())], true)) {
            return;
        }

        $response = $event->getResponse();

        $options = $this->sessionStorageOptions;

        $response->headers->setCookie(new Cookie(
            $session->getName(),
            $session->getId(),
            $options['cookie_lifetime'] ?? 0,
            $options['cookie_path'] ?? '/',
            $options['cookie_domain'] ?? '',
            ($options['cookie_secure'] ?? 'auto') === 'auto'
                ? $request->isSecure() : (bool) ($options['cookie_secure'] ?? 'auto'),
            $options['cookie_httponly'] ?? true,
            false,
            $options['cookie_samesite'] ?? null
        ));
    }

    private function closeSession(): void
    {
        $session = $this->session;
        if ($session->isStarted()) {
            $session->save();
        }

        \session_unset();
    }
}
