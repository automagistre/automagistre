<?php

declare(strict_types=1);

namespace App\Sms\Controller;

use App\Shared\Doctrine\Registry;
use App\Sms\Entity\SmsId;
use App\Sms\Entity\SmsStatus;
use App\State;
use App\Tenant\Tenant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CallbackController
{
    private Registry $registry;

    private State $state;

    public function __construct(Registry $registry, State $state)
    {
        $this->registry = $registry;
        $this->state = $state;
    }

    /**
     * @Route("/callback/{provider}/{id}", name="sms_callback")
     */
    public function __invoke(Request $request, string $provider, string $id): Response
    {
        // TODO Oh shit...
        $this->state->tenant(Tenant::msk());
        $em = $this->registry->manager(SmsStatus::class);

        $em->persist(
            new SmsStatus(
                SmsId::fromString($id),
                [
                    'provider' => $provider,
                    'content' => $request->request->all(),
                ]
            )
        );
        $em->flush();

        return new Response();
    }
}
