<?php

declare(strict_types=1);

namespace App\Sms\Controller;

use App\Doctrine\Registry;
use App\Sms\Entity\SmsId;
use App\Sms\Entity\SmsStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CallbackController
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * @Route("/callback/{provider}/{id}", name="sms_callback", requirements={"provider": "smsaero"})
     */
    public function __invoke(Request $request, string $provider, string $id): Response
    {
        $em = $this->registry->manager(SmsStatus::class);

        $em->persist(
            new SmsStatus(
                SmsId::from($id),
                [
                    'provider' => $provider,
                    'content' => $request->request->all(),
                ],
            ),
        );
        $em->flush();

        return new Response();
    }
}
