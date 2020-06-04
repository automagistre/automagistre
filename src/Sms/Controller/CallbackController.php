<?php

declare(strict_types=1);

namespace App\Sms\Controller;

use App\Shared\Doctrine\Registry;
use App\Sms\Entity\SmsId;
use App\Sms\Entity\SmsStatus;
use Sentry\Util\JSON;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CallbackController
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @Route("/callback/{provider}/{id}", name="sms_callback")
     */
    public function __invoke(Request $request, string $provider, string $id): Response
    {
        $em = $this->registry->manager(SmsStatus::class);

        $em->persist(
            new SmsStatus(
                SmsId::fromString($id),
                [
                    'provider' => $provider,
                    'content' => JSON::decode($request->getContent()),
                ]
            )
        );
        $em->flush();

        return new Response();
    }
}
