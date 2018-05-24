<?php

declare(strict_types=1);

namespace App\Router;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BrandListener implements EventSubscriberInterface
{
    public const BRAND_SESSION_ATTRIBUTE = '_car_brand';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();

        $brand = $request->attributes->get('brand');
        if (null === $brand) {
            return;
        }

        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        if (!$session->has(self::BRAND_SESSION_ATTRIBUTE) || $session->get(self::BRAND_SESSION_ATTRIBUTE) !== $brand) {
            $session->set(self::BRAND_SESSION_ATTRIBUTE, $brand);
        }
    }
}
