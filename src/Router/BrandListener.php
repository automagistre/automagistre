<?php

declare(strict_types=1);

namespace App\Router;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BrandListener implements EventSubscriberInterface
{
    private const BRAND_SESSION = '_car_brand';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }
        $session = $request->getSession();

        $brand = $request->attributes->get('brand');
        $brandSession = $session->get(self::BRAND_SESSION);

        if (null !== $brand && $brandSession !== $brand) {
            $session->set(self::BRAND_SESSION, $brand);
        }

        if (null === $brand && null !== $brandSession) {
            $request->attributes->set('brand', $brandSession);
        }
    }
}
