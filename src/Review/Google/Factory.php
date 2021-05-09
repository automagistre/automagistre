<?php

declare(strict_types=1);

namespace App\Review\Google;

use Google_Client;
use Symfony\Component\Routing\RouterInterface;

final class Factory
{
    public function __construct(private RouterInterface $router, private array $googleCredentials)
    {
    }

    public function client(): Google_Client
    {
        return new Google_Client(
            [
                'application_name' => 'Automagistre Tenant',
                'credentials' => $this->googleCredentials,
                'redirect_uri' => $this->router->generate('easyadmin', [
                    'entity' => 'GoogleReviewToken',
                    'action' => 'index',
                ], RouterInterface::ABSOLUTE_URL),
                'scopes' => [
                    'https://www.googleapis.com/auth/plus.business.manage',
                ],
                'access_type' => 'offline',
            ],
        );
    }
}
